<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanResource\Pages;
use App\Filament\Resources\PeminjamanResource\RelationManagers;
use App\Models\Peminjaman;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use App\Models\Pengembalian;
use Illuminate\Support\HtmlString;


class PeminjamanResource extends Resource
{
    protected static ?string $modelLabel = 'Peminjaman';
    protected static ?string $pluralModelLabel = 'Peminjaman';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pelanggan & Pengiriman')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Nama Penyewa')
                            ->disabled(),
                        Select::make('alamat_user_id')
                            ->relationship('alamat_user', 'label_alamat')
                            ->label('Alamat Pengiriman')
                            ->disabled(),
                    ])->columns(2),

                Section::make('Detail Waktu & Biaya')
                    ->schema([
                        DatePicker::make('tanggal_pesan')
                            ->disabled(),
                        DatePicker::make('tanggal_kembali_rencana')
                            ->disabled(),
                        TextInput::make('total_biaya_sewa')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                    ])->columns(3),

                Section::make('Kontrol Status Transaksi')
                    ->schema([
                        Select::make('status_peminjaman')
                            ->options([
                                'pending'                         => 'Menunggu Persetujuan',
                                'disetujui'                       => 'Disetujui (Sudah Lunas)',
                                'ditolak'                         => 'Ditolak',
                                'aktif'                           => 'Aktif (Barang Sedang Disewa)',
                                'menunggu_pengecekan'             => 'Menunggu Pengecekan Barang',
                                'menunggu_pelunasan'              => 'Menunggu Pelunasan Denda',
                                'menunggu_konfirmasi_denda'       => 'Menunggu Konfirmasi Denda',
                                'selesai'                         => 'Selesai (Barang Telah Kembali)',
                            ])
                            ->required()
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('Order ID')
                    ->prefix('ORD-')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Penyewa')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tanggal_pesan')
                    ->date()
                    ->sortable(),
                TextColumn::make('total_biaya_sewa')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status_peminjaman')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'                         => 'gray',
                        'disetujui'                       => 'info',
                        'ditolak'                         => 'danger',
                        'aktif'                           => 'warning',
                        'menunggu_pengecekan'             => 'warning',
                        'menunggu_pelunasan'              => 'danger',
                        'menunggu_konfirmasi_denda'       => 'warning',
                        'selesai'                         => 'success',
                        default                           => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                // ============================================
                // ACTION 1: SERAHKAN BARANG
                // ============================================
                Action::make('serahkanBarang')
                    ->label('Serahkan Barang')
                    ->icon('heroicon-o-truck')
                    ->color('warning')
                    ->visible(fn ($record) => $record->status_peminjaman === 'disetujui')
                    ->form([
                        FileUpload::make('foto_kondisi_awal')
                            ->label('Foto Kondisi Sebelum Disewakan')
                            ->directory('kondisi_awal')
                            ->image()
                            ->required(),
                    ])
                    ->action(function (array $data, $record): void {
                        $record->update([
                            'status_peminjaman' => 'aktif',
                            'foto_kondisi_awal' => $data['foto_kondisi_awal'],
                        ]);

                        foreach ($record->detail_peminjaman as $detail) {
                            if ($detail->unit_barang) {
                                $detail->unit_barang->update(['status_ketersediaan' => 'disewa']);
                            }
                        }

                        Notification::make()
                            ->title('Barang Berhasil Diserahkan!')
                            ->success()
                            ->send();
                    }),

                // ============================================
                // ACTION 2: TERIMA & CEK BARANG (DARI PENYEWA)
                // ============================================
                Action::make('terimaBarang')
                    ->label('Verifikasi Pengembalian')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => $record->status_peminjaman === 'menunggu_pengecekan')
                    ->mountUsing(function ($form, $record) {
                        $tglRencana = Carbon::parse($record->tanggal_kembali_rencana);
                        $tglAktual  = Carbon::now();
                        $hariTelat  = $tglAktual->gt($tglRencana) ? (int) $tglRencana->diffInDays($tglAktual) : 0;

                        $form->fill([
                            'info_keterlambatan' => $hariTelat > 0 ? "Terlambat: {$hariTelat} Hari" : "Tepat Waktu",
                            'denda_fisik'        => 0,
                        ]);
                    })
                    ->form(function ($record) {
                        $pengembalian = $record->pengembalian;

                        // Siapkan HTML untuk info dari penyewa
                        $kondisiMap = [
                            'baik'         => '✓ Baik - Tidak ada kerusakan',
                            'rusak_ringan' => '⚠ Rusak Ringan - Ada goresan/lecet',
                            'rusak_berat'  => '✗ Rusak Berat - Ada kerusakan signifikan',
                        ];

                        $kondisiUserText = $pengembalian 
                            ? ($kondisiMap[$pengembalian->kondisi_barang_kembali] ?? '-')
                            : '-';

                        $tanggalUser = $pengembalian 
                            ? Carbon::parse($pengembalian->tanggal_kembali_aktual)->format('d M Y')
                            : '-';

                        // Foto dari penyewa
                        $fotoHtml = '';
                        if ($pengembalian && $pengembalian->foto_kondisi_kembali) {
                            $fotoUrl = asset('storage/' . $pengembalian->foto_kondisi_kembali);
                            $fotoHtml = "
                                <div class='mt-3'>
                                    <p class='text-sm font-medium text-gray-700 mb-2'>📷 Foto Kondisi dari Penyewa:</p>
                                    <img src='{$fotoUrl}' alt='Foto Pengembalian' 
                                         style='max-width: 100%; max-height: 400px; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.1);'>
                                </div>
                            ";
                        } else {
                            $fotoHtml = "
                                <div class='mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg'>
                                    <p class='text-sm text-yellow-800'>⚠ Penyewa tidak mengupload foto kondisi barang.</p>
                                </div>
                            ";
                        }

                        return [
                            Section::make('📋 Laporan dari Penyewa')
                                ->description('Informasi yang dilaporkan oleh penyewa saat mengembalikan barang')
                                ->schema([
                                    Placeholder::make('info_penyewa')
                                        ->label('')
                                        ->content(new HtmlString("
                                            <div style='background: #eff6ff; padding: 16px; border-radius: 8px; border: 1px solid #bfdbfe;'>
                                                <div style='margin-bottom: 12px;'>
                                                    <p style='font-size: 13px; color: #374151; margin: 0 0 4px 0;'>📅 Tanggal Pengembalian:</p>
                                                    <p style='font-size: 16px; font-weight: 700; color: #111827; margin: 0;'>{$tanggalUser}</p>
                                                </div>
                                                <div style='margin-bottom: 12px;'>
                                                    <p style='font-size: 13px; color: #374151; margin: 0 0 4px 0;'>📦 Kondisi yang Dilaporkan Penyewa:</p>
                                                    <p style='font-size: 16px; font-weight: 700; color: #111827; margin: 0;'>{$kondisiUserText}</p>
                                                </div>
                                                {$fotoHtml}
                                            </div>
                                        ")),
                                ]),

                            Section::make('✅ Verifikasi & Penilaian Admin')
                                ->description('Admin melakukan pengecekan dan penilaian akhir kondisi barang')
                                ->schema([
                                    TextInput::make('info_keterlambatan')
                                        ->disabled()
                                        ->label('Status Waktu'),
                                    Select::make('kondisi_barang_aktual')
                                        ->options([
                                            'baik'         => 'Baik - Sesuai laporan penyewa',
                                            'rusak_ringan' => 'Rusak Ringan - Ada goresan/lecet',
                                            'rusak_berat'  => 'Rusak Berat - Ada kerusakan signifikan',
                                        ])
                                        ->label('Kondisi Barang (Verifikasi Admin)')
                                        ->default($pengembalian?->kondisi_barang_kembali ?? 'baik')
                                        ->required(),
                                    TextInput::make('denda_fisik')
                                        ->numeric()
                                        ->prefix('Rp')
                                        ->default(0)
                                        ->label('Denda Kerusakan Fisik (jika ada)')
                                        ->helperText('Masukkan nominal jika ada kerusakan fisik yang perlu didenda'),
                                    Textarea::make('catatan')
                                        ->label('Catatan Admin')
                                        ->rows(3)
                                        ->default($pengembalian?->catatan),
                                ]),
                        ];
                    })
                    ->action(function (array $data, $record): void {
                        $tglRencana        = Carbon::parse($record->tanggal_kembali_rencana);
                        $tglAktual         = Carbon::now();
                        $hariTelat         = $tglAktual->gt($tglRencana) ? (int) $tglRencana->diffInDays($tglAktual) : 0;
                        $tarifDendaPerHari = 50000; // Sesuaikan dengan tarif denda per hari
                        $totalDendaTelat   = $hariTelat * $tarifDendaPerHari;
                        $dendaFisik        = (int) ($data['denda_fisik'] ?? 0);
                        $totalDendaAkhir   = $totalDendaTelat + $dendaFisik;

                        // Update record pengembalian yang sudah dibuat oleh user
                        $pengembalian = $record->pengembalian;
                        
                        if ($pengembalian) {
                            $pengembalian->update([
                                'kondisi_barang_kembali' => $data['kondisi_barang_aktual'] ?? $pengembalian->kondisi_barang_kembali,
                                'jumlah_hari_telat'      => $hariTelat,
                                'tarif_denda_per_hari'   => $tarifDendaPerHari,
                                'total_denda'            => $totalDendaAkhir,
                                'status_denda'           => $totalDendaAkhir > 0 ? 'belum_bayar' : 'tidak_ada_denda',
                                'catatan'                => $data['catatan'] ?? null,
                            ]);
                        } else {
                            // Fallback: buat baru jika belum ada
                            Pengembalian::create([
                                'peminjaman_id'          => $record->id,
                                'tanggal_kembali_aktual' => $tglAktual->format('Y-m-d'),
                                'kondisi_barang_kembali' => $data['kondisi_barang_aktual'] ?? 'baik',
                                'jumlah_hari_telat'      => $hariTelat,
                                'tarif_denda_per_hari'   => $tarifDendaPerHari,
                                'total_denda'            => $totalDendaAkhir,
                                'status_denda'           => $totalDendaAkhir > 0 ? 'belum_bayar' : 'tidak_ada_denda',
                                'catatan'                => $data['catatan'] ?? null,
                            ]);
                        }

                        // Update status peminjaman
                        if ($totalDendaAkhir > 0) {
                            $record->update(['status_peminjaman' => 'menunggu_pelunasan']);
                        } else {
                            $record->update(['status_peminjaman' => 'selesai']);
                            
                            // Kembalikan status ketersediaan unit
                            foreach ($record->detail_peminjaman as $detail) {
                                $detail->unit_barang?->update(['status_ketersediaan' => 'tersedia']);
                            }
                        }

                        Notification::make()
                            ->title('Pengembalian Berhasil Diverifikasi!')
                            ->body($totalDendaAkhir > 0 
                                ? 'Total denda: Rp ' . number_format($totalDendaAkhir, 0, ',', '.') . ' - Menunggu pelunasan dari penyewa.'
                                : 'Tidak ada denda. Transaksi selesai.')
                            ->success()
                            ->send();
                    })
                    ->modalWidth('3xl'),

                // ============================================
                // ACTION 3: KONFIRMASI PELUNASAN DENDA
                // ============================================
                Action::make('konfirmasi_pelunasan_denda')
                    ->label('Konfirmasi Denda')
                    ->icon('heroicon-o-currency-dollar')
                    ->color('info')
                    ->visible(fn ($record) => $record->status_peminjaman === 'menunggu_konfirmasi_denda')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pelunasan Denda')
                    ->modalDescription('Pastikan pembayaran denda sudah masuk. Dengan mengonfirmasi ini, transaksi akan dianggap selesai.')
                    ->modalSubmitActionLabel('Ya, Konfirmasi')
                    ->action(function ($record) {
                        // 1. Update status peminjaman ke selesai
                        $record->update(['status_peminjaman' => 'selesai']);
                        
                        // 2. Update status denda di tabel pengembalian
                        $record->pengembalian?->update(['status_denda' => 'sudah_bayar']);

                        // 3. Kembalikan status ketersediaan unit
                        foreach ($record->detail_peminjaman as $detail) {
                            $detail->unit_barang?->update(['status_ketersediaan' => 'tersedia']);
                        }

                        Notification::make()
                            ->title('Denda Lunas!')
                            ->body('Transaksi telah diselesaikan.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DetailPeminjamansRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPeminjamen::route('/'),
            'create' => Pages\CreatePeminjaman::route('/create'),
            'edit'   => Pages\EditPeminjaman::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}