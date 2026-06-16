<?php

namespace App\Filament\Resources\UnitBarangResource\Pages;

use App\Filament\Resources\UnitBarangResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnitBarang extends EditRecord
{
    protected static string $resource = UnitBarangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
