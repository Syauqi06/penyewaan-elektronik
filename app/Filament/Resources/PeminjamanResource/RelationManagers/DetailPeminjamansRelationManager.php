<?php

namespace App\Filament\Resources\PeminjamanResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailPeminjamansRelationManager extends RelationManager
{
    protected static string $relationship = 'detail_peminjamans';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('unit_barang.katalog_barang.nama_barang')
                    ->label('Nama Barang')
                    ->sortable(),
                TextColumn::make('unit_barang.serial_number')
                    ->label('Serial Number')
                    ->badge()
                    ->color('info'),
                TextColumn::make('harga_sewa_satuan')
                    ->label('Harga Sewa (Satuan)')
                    ->money('IDR'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([                
            ])
            ->bulkActions([
            ]);
    }
}
