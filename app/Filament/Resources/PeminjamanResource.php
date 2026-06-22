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
                            ->disabled() // Dikunci agar admin tidak bisa mengganti nama penyewa di tengah transaksi
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
                                'pending' => 'Menunggu Persetujuan',
                                'disetujui' => 'Disetujui (Menunggu DP)',
                                'ditolak' => 'Ditolak',
                                'aktif' => 'Aktif (Barang Sedang Disewa)',
                                'selesai' => 'Selesai (Barang Telah Kembali)',
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
                        'pending' => 'gray',
                        'disetujui' => 'info',
                        'ditolak' => 'danger',
                        'aktif' => 'warning',
                        'selesai' => 'success',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Action::make('serahkanBarang')
                    ->label('Serahkan Barang')
                    ->icon('heroicon-o-truck')
                    ->color('warning')
                    // Cuma muncul kalau statusnya 'disetujui' (Sudah bayar DP)
                    ->visible(fn ($record) => $record->status_peminjaman === 'disetujui') 
                    ->form([
                        FileUpload::make('foto_kondisi_awal')
                            ->label('Foto Kondisi Sebelum Disewakan')
                            ->directory('kondisi_awal')
                            ->image()
                            ->helperText('Upload foto bukti kondisi barang sebelum diserahkan ke penyewa.')
                            ->required(),
                    ])
                    ->action(function (array $data, $record): void {
                        // Ubah status jadi aktif dan simpan foto awal
                        $record->update([
                            'status_peminjaman' => 'aktif',
                            'foto_kondisi_awal' => $data['foto_kondisi_awal'],
                        ]);

                        // Ubah status ketersediaan unit barang jadi 'disewa'
                        foreach ($record->detail_peminjaman as $detail) {
                            if ($detail->unit_barang) {
                                $detail->unit_barang->update(['status_ketersediaan' => 'disewa']); // Atau sesuaikan dengan enum lu
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

                Action::make('prosesPengembalian')
                    ->label('Terima Barang')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    // Tombol ini cuma muncul kalau statusnya lagi disewa (aktif)
                    ->visible(fn ($record) => $record->status_peminjaman === 'aktif') 
                    ->form([
                        // 1. Info otomatis denda telat pakai Placeholder
                        Placeholder::make('info_keterlambatan')
                            ->label('Log Keterlambatan (Otomatis)')
                            ->content(function ($record) {
                                $tglRencana = Carbon::parse($record->tanggal_kembali_rencana);
                                $tglAktual = Carbon::now();
                                $hariTelat = $tglAktual->gt($tglRencana) ? $tglAktual->diffInDays($tglRencana) : 0;
                                
                                return "Jadwal: {$tglRencana->format('d M Y')} | Aktual: {$tglAktual->format('d M Y')} | Telat: {$hariTelat} Hari";
                            })
                            ->columnSpanFull(),

                        // 2. Input Manual Admin
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
                            ->nullable(),
                        Textarea::make('catatan')
                            ->label('Catatan Opsional')
                            ->nullable(),
                    ])
                    ->action(function (array $data, $record): void {
                        // LOGIKA BACKEND DIEKSEKUSI DI SINI PAS TOMBOL SUBMIT DIKLIK

                        // 1. Kalkulasi Ulang Telat
                        $tglRencana = Carbon::parse($record->tanggal_kembali_rencana);
                        $tglAktual = Carbon::now();
                        $hariTelat = $tglAktual->gt($tglRencana) ? $tglAktual->diffInDays($tglRencana) : 0;
                        
                        $tarifDendaPerHari = 50000; // Bisa disesuaikan
                        $totalDendaTelat = $hariTelat * $tarifDendaPerHari;
                        $totalDendaAkhir = $totalDendaTelat + $data['denda_fisik'];

                        // 2. Simpan ke Tabel Pengembalian
                        Pengembalian::create([
                            'peminjaman_id' => $record->id,
                            'tanggal_kembali_aktual' => $tglAktual->format('Y-m-d'),
                            'kondisi_barang_kembali' => $data['kondisi_barang_kembali'],
                            'foto_kondisi_kembali' => $data['foto_kondisi_kembali'],
                            'jumlah_hari_telat' => $hariTelat,
                            'tarif_denda_per_hari' => $tarifDendaPerHari,
                            'total_denda' => $totalDendaAkhir,
                            'status_denda' => $totalDendaAkhir > 0 ? 'belum_bayar' : 'tidak_ada',
                            'catatan' => $data['catatan'],
                        ]);

                        // 3. Update Status Peminjaman
                        $record->update(['status_peminjaman' => 'selesai']);

                        // 4. Bebaskan Unit Barang
                        foreach ($record->detail_peminjaman as $detail) {
                            if ($detail->unit_barang) {
                                $detail->unit_barang->update(['status_ketersediaan' => 'tersedia']);
                            }
                        }

                        // 5. Kirim Notifikasi Sukses ke Layar Admin
                        Notification::make()
                            ->title('Barang Berhasil Dikembalikan!')
                            ->success()
                            ->body('Unit barang sekarang sudah tersedia untuk disewa kembali.')
                            ->send();
                    })
                    ->requiresConfirmation() // Opsional: Bikin popup konfirmasi "Are you sure?"
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
            'index' => Pages\ListPeminjamen::route('/'),
            'create' => Pages\CreatePeminjaman::route('/create'),
            'edit' => Pages\EditPeminjaman::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
