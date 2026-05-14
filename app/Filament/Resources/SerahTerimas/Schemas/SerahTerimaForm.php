<?php

namespace App\Filament\Resources\SerahTerimas\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SerahTerimaForm
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
                Select::make('jenis')
                    ->options(['penyerahan' => 'Penyerahan', 'pengembalian' => 'Pengembalian'])
                    ->required(),
                DateTimePicker::make('tgl_serah')
                    ->required(),
                Textarea::make('tanda_tangan_user')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('status')
                    ->required()
                    ->default('selesai'),
                Textarea::make('catatan')
                    ->columnSpanFull(),
            ]);
    }
}
