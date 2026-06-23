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
                            ->disabled()
                            ->required(),
                        Select::make('alamat_user_id')
                            ->relationship('alamat_user', 'label_alamat')
                            ->label('Alamat Pengiriman')
                            ->disabled()
                            ->required(),
                    ])->columns(2),

                Section::make('Detail Waktu & Biaya')
                    ->schema([
                        DatePicker::make('tanggal_pesan')
                            ->disabled()
                            ->required(),
                        DatePicker::make('tanggal_kembali_rencana')
                            ->disabled()
                            ->required(),
                        TextInput::make('total_biaya_sewa')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->required(),
                        TextInput::make('jumlah_dp')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->required(),
                        TextInput::make('jumlah_deposit')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->required(),
                    ])->columns(3),

                Section::make('Kontrol Status Transaksi')
                    ->schema([
                        Select::make('status_peminjaman')
                            ->options([
                                'pending'            => 'Menunggu Persetujuan',
                                'disetujui'          => 'Disetujui (Menunggu DP)',
                                'ditolak'            => 'Ditolak',
                                'aktif'              => 'Aktif (Barang Sedang Disewa)',
                                'menunggu_refund'    => 'Menunggu Refund Deposit', // Tambahan Baru
                                'menunggu_pelunasan' => 'Menunggu Pelunasan Sisa', // Tambahan Baru
                                'selesai'            => 'Selesai (Barang Telah Kembali)',
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
                        'pending'            => 'gray',
                        'disetujui'          => 'info',
                        'ditolak'            => 'danger',
                        'aktif'              => 'warning',
                        'menunggu_refund'    => 'warning', // Tambahan Baru
                        'menunggu_pelunasan' => 'danger',  // Tambahan Baru
                        'selesai'            => 'success',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                // ACTION 1: SERAHKAN BARANG
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
                            ->imageEditor()
                            ->helperText('Upload foto bukti kondisi barang sebelum diserahkan ke penyewa.')
                            ->required(),
                    ])
                    ->action(function (array $data, $record): void {
                        $foto = null;
                        if (!empty($data['foto_kondisi_awal'])) {
                            $foto = is_array($data['foto_kondisi_awal'])
                                ? collect($data['foto_kondisi_awal'])->first()
                                : $data['foto_kondisi_awal'];
                        }

                        $record->update([
                            'status_peminjaman' => 'aktif',
                            'foto_kondisi_awal' => $foto,
                        ]);

                        foreach ($record->detail_peminjaman as $detail) {
                            if ($detail->unit_barang) {
                                $detail->unit_barang->update(['status_ketersediaan' => 'disewa']);
                            }
                        }

                        Notification::make()
                            ->title('Barang berhasil diserahkan!')
                            ->success()
                            ->body('Status pesanan sekarang menjadi Aktif (Sedang Disewa).')
                            ->send();
                    })
                    ->modalHeading('Penyerahan Barang ke Penyewa')
                    ->modalWidth('md'),

                // ACTION 2: PROSES PENGEMBALIAN & HITUNG REFUND
                Action::make('prosesPengembalian')
                    ->label('Terima Barang')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => $record->status_peminjaman === 'aktif')
                    ->mountUsing(function ($form, $record) {
                        $tglRencana = Carbon::parse($record->tanggal_kembali_rencana);
                        $tglAktual  = Carbon::now();
                        $hariTelat  = $tglAktual->gt($tglRencana) ? (int) $tglAktual->diffInDays($tglRencana) : 0;

                        $form->fill([
                            'info_keterlambatan' => "Jadwal: {$tglRencana->format('d M Y')} | Aktual: {$tglAktual->format('d M Y')} | Telat: {$hariTelat} Hari",
                            'denda_fisik'        => 0,
                        ]);
                    })
                    ->form([
                        TextInput::make('info_keterlambatan')
                            ->label('Log Keterlambatan (Otomatis)')
                            ->disabled()
                            ->columnSpanFull(),

                        Textarea::make('kondisi_barang_kembali')
                            ->label('Kondisi Fisik Barang')
                            ->required(),

                        TextInput::make('denda_fisik')
                            ->label('Denda Kerusakan (Rp)')
                            ->numeric()
                            ->default(0)
                            ->helperText('Isi 0 jika tidak ada cacat/kerusakan baru.')
                            ->required(),

                        FileUpload::make('foto_kondisi_kembali')
                            ->label('Upload Foto Bukti')
                            ->directory('pengembalian')
                            ->image()
                            ->imageEditor()
                            ->nullable(),

                        Textarea::make('catatan')
                            ->label('Catatan Opsional')
                            ->nullable(),
                    ])
                    ->action(function (array $data, $record): void {
                        $tglRencana        = Carbon::parse($record->tanggal_kembali_rencana);
                        $tglAktual         = Carbon::now();
                        $hariTelat         = $tglAktual->gt($tglRencana) ? (int) $tglAktual->diffInDays($tglRencana) : 0;
                        $tarifDendaPerHari = 50000;
                        $totalDendaTelat   = $hariTelat * $tarifDendaPerHari;
                        $totalDendaAkhir   = $totalDendaTelat + (int) $data['denda_fisik'];

                        // 🔥 LOGIKA KALKULASI REFUND DEPOSIT 🔥
                        $sisaSewaBelumDibayar = $record->total_biaya_sewa - $record->jumlah_dp;
                        $totalPotongan        = $sisaSewaBelumDibayar + $totalDendaAkhir;
                        $nominalRefund        = $record->jumlah_deposit - $totalPotongan;

                        $statusPeminjaman = 'selesai';
                        $statusRefund     = 'tidak_ada';

                        if ($nominalRefund > 0) {
                            $statusPeminjaman = 'menunggu_refund';
                            $statusRefund     = 'menunggu';
                        } elseif ($nominalRefund < 0) {
                            $statusPeminjaman = 'menunggu_pelunasan';
                        }

                        // Handle Foto
                        $fotoKembali = null;
                        if (!empty($data['foto_kondisi_kembali'])) {
                            $fotoKembali = is_array($data['foto_kondisi_kembali'])
                                ? collect($data['foto_kondisi_kembali'])->first()
                                : $data['foto_kondisi_kembali'];
                        }

                        Pengembalian::create([
                            'peminjaman_id'          => $record->id,
                            'tanggal_kembali_aktual' => $tglAktual->format('Y-m-d'),
                            'kondisi_barang_kembali' => $data['kondisi_barang_kembali'],
                            'foto_kondisi_kembali'   => $fotoKembali,
                            'jumlah_hari_telat'      => $hariTelat,
                            'tarif_denda_per_hari'   => $tarifDendaPerHari,
                            'total_denda'            => $totalDendaAkhir,
                            'status_denda'           => $totalDendaAkhir > 0 ? 'belum_bayar' : 'tidak_ada',
                            'nominal_refund'         => max(0, $nominalRefund), // Nilai ga boleh minus di kolom ini
                            'status_refund'          => $statusRefund,
                            'catatan'                => $data['catatan'] ?? null,
                        ]);

                        $record->update(['status_peminjaman' => $statusPeminjaman]);

                        // Bebaskan barang hanya kalau statusnya selesai atau nunggu refund
                        if ($statusPeminjaman === 'menunggu_refund' || $statusPeminjaman === 'selesai') {
                            foreach ($record->detail_peminjaman as $detail) {
                                if ($detail->unit_barang) {
                                    $detail->unit_barang->update(['status_ketersediaan' => 'tersedia']);
                                }
                            }
                        }

                        Notification::make()
                            ->title('Barang Berhasil Dikembalikan!')
                            ->success()
                            ->body('Kalkulasi denda selesai. Silakan periksa status selanjutnya.')
                            ->send();
                    })
                    ->requiresConfirmation(false)
                    ->modalHeading('Proses Pengembalian Barang')
                    ->modalWidth('2xl'),

                // ACTION 3: KIRIM REFUND KE CUSTOMER
                Action::make('kirimRefund')
                    ->label('Kirim Refund')
                    ->icon('heroicon-o-banknotes')
                    ->color('info')
                    ->visible(fn ($record) => $record->status_peminjaman === 'menunggu_refund') 
                    ->form([
                        Placeholder::make('info_rekening')
                            ->label('Informasi Transfer Customer')
                            ->content(function ($record) {
                                $user = $record->user;
                                $refund = $record->pengembalian->nominal_refund ?? 0;
                                // Desain nota simpel pakai inline-style biar 100% aman di Filament
                                return new \Illuminate\Support\HtmlString("
                                    <div style='padding: 1.5rem; background-color: #f8fafc; border-radius: 0.75rem; border: 1px solid #e2e8f0;'>
                                        <p style='font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;'>Bank Tujuan:</p>
                                        <p style='font-weight: bold; font-size: 1.25rem; color: #0f172a; margin-bottom: 0.25rem;'>{$user->nama_bank} - {$user->nomor_rekening}</p>
                                        <p style='font-weight: 600; font-size: 0.875rem; text-transform: uppercase; color: #475569;'>A.N: {$user->atas_nama_rekening}</p>
                                        
                                        <div style='margin-top: 1rem; padding-top: 1rem; border-top: 2px dashed #cbd5e1;'>
                                            <p style='font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;'>Nominal Wajib Transfer:</p>
                                            <p style='font-weight: 900; font-size: 1.875rem; color: #dc2626;'>Rp " . number_format($refund, 0, ',', '.') . "</p>
                                        </div>
                                    </div>
                                ");
                            })->columnSpanFull(),

                        FileUpload::make('bukti_refund')
                            ->label('Upload Bukti Transfer (Screenshot M-Banking)')
                            ->directory('bukti_refund')
                            ->image()
                            ->imageEditor()
                            ->required(),
                    ])
                    ->action(function (array $data, $record): void {
                        $fotoRefund = null;
                        if (!empty($data['bukti_refund'])) {
                            $fotoRefund = is_array($data['bukti_refund'])
                                ? collect($data['bukti_refund'])->first()
                                : $data['bukti_refund'];
                        }

                        $record->pengembalian()->update([
                            'bukti_refund' => $fotoRefund,
                            'status_refund' => 'selesai',
                        ]);

                        $record->update(['status_peminjaman' => 'selesai']);

                        Notification::make()
                            ->title('Refund Berhasil Dikirim!')
                            ->success()
                            ->body('Status pesanan sekarang menjadi Selesai.')
                            ->send();
                    })
                    ->modalHeading('Kirim Pengembalian Deposit')
                    ->modalWidth('md'),
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