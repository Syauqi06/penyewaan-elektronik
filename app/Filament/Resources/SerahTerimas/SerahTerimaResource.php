<?php

namespace App\Filament\Resources\SerahTerimas;

use App\Filament\Resources\SerahTerimas\Pages\CreateSerahTerima;
use App\Filament\Resources\SerahTerimas\Pages\EditSerahTerima;
use App\Filament\Resources\SerahTerimas\Pages\ListSerahTerimas;
use App\Filament\Resources\SerahTerimas\Schemas\SerahTerimaForm;
use App\Filament\Resources\SerahTerimas\Tables\SerahTerimasTable;
use App\Models\SerahTerima;
use BackedEnum;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class SerahTerimaResource extends Resource
{
    protected static ?string $model = SerahTerima::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SerahTerimaForm::configure($schema)
            ->schema([
                Forms\Components\Select::make('peminjaman_id')
                    ->relationship('peminjaman', 'id')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('admin', 'name')
                    ->required()
                    ->label('Admin yang Bertugas'),
                Forms\Components\Select::make('jenis')
                    ->options([
                        'penyerahan' => 'Penyerahan Barang',
                        'pengembalian' => 'Pengembalian Barang',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('tgl_serah')
                    ->required(),
                
                // INI ADALAH BAGIAN TANDA TANGAN DIGITALNYA
                SignaturePad::make('tanda_tangan_user')
                    ->label('Tanda Tangan Penyewa')
                    ->dotSize(2.0)
                    ->lineMinWidth(0.5)
                    ->lineMaxWidth(2.5)
                    ->penColor('#000000') // Warna tinta
                    ->backgroundColor('#f3f4f6') // Warna latar belakang pad
                    ->required()
                    ->columnSpanFull(), // Agar form tanda tangan membentang penuh

                Forms\Components\TextInput::make('status')
                    ->required()
                    ->default('selesai'),
                Forms\Components\Textarea::make('catatan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return SerahTerimasTable::configure($table);
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
            'index' => ListSerahTerimas::route('/'),
            'create' => CreateSerahTerima::route('/create'),
            'edit' => EditSerahTerima::route('/{record}/edit'),
        ];
    }
}
