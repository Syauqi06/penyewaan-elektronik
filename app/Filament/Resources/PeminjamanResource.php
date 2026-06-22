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
    protected static ?string $model = Peminjaman::class;

    protected static ?string $modelLabel = 'Peminjaman';
    protected static ?string $pluralModelLabel = 'Peminjaman';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                                'pending'   => 'Menunggu Persetujuan',
                                'disetujui' => 'Disetujui (Menunggu DP)',
                                'ditolak'   => 'Ditolak',
                                'aktif'     => 'Aktif (Barang Sedang Disewa)',
                                'selesai'   => 'Selesai (Barang Telah Kembali)',
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
                        'pending'   => 'gray',
                        'disetujui' => 'info',
                        'ditolak'   => 'danger',
                        'aktif'     => 'warning',
                        'selesai'   => 'success',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                // =========================================================
                // ACTION 1: SERAHKAN BARANG
                // FIX: Tidak perlu closure di ->form(), langsung array biasa
                // =========================================================
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
                        // FIX: FileUpload di Filament v3 return array, ambil item pertama
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

                // =========================================================
                // ACTION 2: PROSES PENGEMBALIAN
                // FIX UTAMA: Placeholder dengan $record harus pakai
                // ->mountUsing() agar data ter-inject ke form state dulu,
                // baru dibaca oleh field. Ini mencegah Alpine dispatchEvent error.
                // =========================================================
                Action::make('prosesPengembalian')
                    ->label('Terima Barang')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn ($record) => $record->status_peminjaman === 'aktif')
                    // FIX: Inject data ke form state sebelum modal render
                    ->mountUsing(function ($form, $record) {
                        $tglRencana = Carbon::parse($record->tanggal_kembali_rencana);
                        $tglAktual  = Carbon::now();
                        $hariTelat  = $tglAktual->gt($tglRencana)
                            ? (int) $tglAktual->diffInDays($tglRencana)
                            : 0;

                        // Pre-fill form state, bukan closure di dalam field
                        $form->fill([
                            'info_keterlambatan' => "Jadwal: {$tglRencana->format('d M Y')} | Aktual: {$tglAktual->format('d M Y')} | Telat: {$hariTelat} Hari",
                            'denda_fisik'        => 0,
                        ]);
                    })
                    ->form([
                        // FIX: Pakai TextInput disabled, bukan Placeholder dengan closure $record
                        // Placeholder dengan closure $record menyebabkan Alpine error di Filament v3
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
                        $hariTelat         = $tglAktual->gt($tglRencana)
                            ? (int) $tglAktual->diffInDays($tglRencana)
                            : 0;
                        $tarifDendaPerHari = 50000;
                        $totalDendaTelat   = $hariTelat * $tarifDendaPerHari;
                        $totalDendaAkhir   = $totalDendaTelat + (int) $data['denda_fisik'];

                        // FIX: Handle FileUpload nullable + bisa return array
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
                            'catatan'                => $data['catatan'] ?? null,
                        ]);

                        $record->update(['status_peminjaman' => 'selesai']);

                        foreach ($record->detail_peminjaman as $detail) {
                            if ($detail->unit_barang) {
                                $detail->unit_barang->update(['status_ketersediaan' => 'tersedia']);
                            }
                        }

                        Notification::make()
                            ->title('Barang Berhasil Dikembalikan!')
                            ->success()
                            ->body('Unit barang sekarang sudah tersedia untuk disewa kembali.')
                            ->send();
                    })
                    ->requiresConfirmation(false) // FIX: Matikan dulu, konfirmasi + modal form bisa konflik
                    ->modalHeading('Proses Pengembalian Barang')
                    ->modalWidth('2xl'),
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