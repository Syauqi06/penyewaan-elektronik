<?php

namespace App\Filament\Resources\Dendas\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DendaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('pengembalian_id')
                    ->required()
                    ->numeric(),
                TextInput::make('peminjaman_id')
                    ->required()
                    ->numeric(),
                TextInput::make('jumlah_hari_telat')
                    ->tel()
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('tarif_denda_perhari')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_denda')
                    ->required()
                    ->numeric(),
                Select::make('status_denda')
                    ->options(['belum_lunas' => 'Belum lunas', 'lunas' => 'Lunas'])
                    ->default('belum_lunas')
                    ->required(),
                DateTimePicker::make('tgl_denda')
                    ->required(),
            ]);
    }
}
