<?php

namespace App\Filament\Resources\Barangs\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BarangForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kategori_id')
                    ->required()
                    ->numeric(),
                TextInput::make('nama_barang')
                    ->required(),
                TextInput::make('foto_barang'),
                Textarea::make('deskripsi')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('harga_sewa_perhari')
                    ->required()
                    ->numeric(),
                TextInput::make('stok_tersedia')
                    ->required()
                    ->numeric(),
                Select::make('kondisi')
                    ->options(['baik' => 'Baik', 'rusak_ringan' => 'Rusak ringan', 'rusak_berat' => 'Rusak berat'])
                    ->default('baik')
                    ->required(),
                Select::make('status')
                    ->options(['tersedia' => 'Tersedia', 'disewa' => 'Disewa', 'maintenance' => 'Maintenance'])
                    ->default('tersedia')
                    ->required(),
            ]);
    }
}
