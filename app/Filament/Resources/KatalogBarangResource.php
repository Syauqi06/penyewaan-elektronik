<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KatalogBarangResource\Pages;
use App\Filament\Resources\KatalogBarangResource\RelationManagers;
use App\Models\KatalogBarang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Tables;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KatalogBarangResource extends Resource
{
    protected static ?string $model = KatalogBarang::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('kategori_id')
                    ->relationship('kategori', 'nama_kategori') // Menarik data otomatis dari tabel Kategori
                    ->required()
                    ->searchable()
                    ->preload(),
                TextInput::make('nama_barang')
                    ->required()
                    ->maxLength(255),
                TextInput::make('harga_sewa_per_hari')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                TextInput::make('harga_asli')
                    ->label('Harga Asli Barang')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                FileUpload::make('foto_barang')
                    ->image()
                    ->directory('foto-katalog')
                    ->columnSpanFull(),
                Textarea::make('deskripsi')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto_barang')
                    ->square(),
                TextColumn::make('nama_barang')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('kategori.nama_kategori')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('harga_sewa_per_hari')
                    ->numeric()
                    ->money('IDR')
                    ->sortable(),
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
            'index' => Pages\ListKatalogBarangs::route('/'),
            'create' => Pages\CreateKatalogBarang::route('/create'),
            'edit' => Pages\EditKatalogBarang::route('/{record}/edit'),
        ];
    }
}
