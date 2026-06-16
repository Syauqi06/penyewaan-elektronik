<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitBarangResource\Pages;
use App\Filament\Resources\UnitBarangResource\RelationManagers;
use App\Models\UnitBarang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitBarangResource extends Resource
{
    protected static ?string $model = UnitBarang::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('katalog_barang_id')
                    ->relationship('katalog_barang', 'nama_barang') // Menarik nama barang dari Katalog
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('serial_number')
                    ->required()
                    ->unique(ignoreRecord: true) // Mencegah input SN yang sama, tapi aman saat proses Edit
                    ->maxLength(255),
                TextInput::make('kondisi_fisik')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Misal: Mulus, Lensa sedikit lecet, dll'),
                Select::make('status_ketersediaan')
                    ->options([
                        'tersedia' => 'Tersedia',
                        'disewa' => 'Sedang Disewa',
                        'perawatan' => 'Dalam Perawatan',
                    ])
                    ->default('tersedia')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('katalog_barang.nama_barang')
                    ->label('Nama Barang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('serial_number')
                    ->label('Serial Number')
                    ->searchable(),
                TextColumn::make('kondisi_fisik')
                    ->limit(30),
                TextColumn::make('status_ketersediaan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'tersedia' => 'success',
                        'disewa' => 'warning',
                        'perawatan' => 'danger',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListUnitBarangs::route('/'),
            'create' => Pages\CreateUnitBarang::route('/create'),
            'edit' => Pages\EditUnitBarang::route('/{record}/edit'),
        ];
    }
}
