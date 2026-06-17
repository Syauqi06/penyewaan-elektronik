<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PeminjamanResource\Pages;
use App\Filament\Resources\PeminjamanResource\RelationManagers;
use App\Models\Peminjaman;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PeminjamanResource extends Resource
{
    protected static ?string $model = Peminjaman::class;

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
                Tables\Actions\EditAction::make(),
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
