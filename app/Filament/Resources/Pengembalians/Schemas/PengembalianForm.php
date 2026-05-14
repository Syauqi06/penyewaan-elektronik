<?php

namespace App\Filament\Resources\Pengembalians\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PengembalianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('peminjaman_id')
                    ->required()
                    ->numeric(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                DateTimePicker::make('tgl_kembali_aktual')
                    ->required(),
                TextInput::make('kondisi_barang_kembali')
                    ->required(),
                TextInput::make('foto_kondisi_kembali'),
                Textarea::make('catatan_kerusakan')
                    ->columnSpanFull(),
                Select::make('status_pengembalian')
                    ->options(['aman' => 'Aman', 'rusak' => 'Rusak', 'hilang' => 'Hilang'])
                    ->default('aman')
                    ->required(),
            ]);
    }
}
