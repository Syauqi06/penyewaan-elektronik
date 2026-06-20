<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VerifikasiIdentitasResource\Pages;
use App\Filament\Resources\VerifikasiIdentitasResource\RelationManagers;
use App\Models\VerifikasiIdentitas;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class VerifikasiIdentitasResource extends Resource
{
    protected static ?string $model = VerifikasiIdentitas::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Verifikasi KTP';
    protected static ?string $navigationGroup = 'Manajemen Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detail Verifikasi')
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->disabled()
                            ->label('Nama Penyewa'),
                        Select::make('status')
                            ->options([
                                'pending' => 'Menunggu Pengecekan',
                                'disetujui' => 'Disetujui (Bisa Menyewa)',
                                'ditolak' => 'Ditolak',
                            ])
                            ->required()
                            ->native(false)
                            ->label('Status Verifikasi'),
                        FileUpload::make('foto_ktp')
                            ->image()
                            ->disabled()
                            ->deletable(false)
                            ->label('Dokumen KTP (Periksa Keasliannya)'),
                        Textarea::make('catatan')
                            ->label('Catatan Admin (Wajib diisi jika KTP ditolak)')
                            ->placeholder('Contoh: Foto buram, KTP kadaluarsa, dll...')
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Penyewa')
                    ->searchable()
                    ->sortable(),
                ImageColumn::make('foto_ktp')
                    ->label('Foto KTP')
                    ->size(80),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'disetujui' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                TextColumn::make('created_at')
                    ->dateTime('d M Y H:i')
                    ->label('Waktu Upload')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Periksa'),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVerifikasiIdentitas::route('/'),
            'create' => Pages\CreateVerifikasiIdentitas::route('/create'),
            'edit' => Pages\EditVerifikasiIdentitas::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
