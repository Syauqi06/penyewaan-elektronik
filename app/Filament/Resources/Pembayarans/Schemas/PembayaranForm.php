<?php

namespace App\Filament\Resources\Pembayarans\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PembayaranForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('peminjaman_id')
                    ->required()
                    ->numeric(),
                TextInput::make('id_transaksi_midtrans'),
                TextInput::make('jumlah_bayar')
                    ->required()
                    ->numeric(),
                Select::make('jenis_pembayaran')
                    ->options(['dp' => 'Dp', 'pelunasan' => 'Pelunasan', 'denda' => 'Denda'])
                    ->required(),
                TextInput::make('metode_pembayaran'),
                TextInput::make('bukti_pembayaran'),
                Select::make('status_pembayaran')
                    ->options(['pending' => 'Pending', 'success' => 'Success', 'failed' => 'Failed', 'expired' => 'Expired'])
                    ->default('pending')
                    ->required(),
                DateTimePicker::make('tgl_bayar'),
            ]);
    }
}
