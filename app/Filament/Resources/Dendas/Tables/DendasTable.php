<?php

namespace App\Filament\Resources\Dendas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DendasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('pengembalian_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('peminjaman_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('jumlah_hari_telat')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tarif_denda_perhari')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_denda')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status_denda')
                    ->badge(),
                TextColumn::make('tgl_denda')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
