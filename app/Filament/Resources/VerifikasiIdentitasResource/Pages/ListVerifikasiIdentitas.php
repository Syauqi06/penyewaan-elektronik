<?php

namespace App\Filament\Resources\VerifikasiIdentitasResource\Pages;

use App\Filament\Resources\VerifikasiIdentitasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVerifikasiIdentitas extends ListRecords
{
    protected static string $resource = VerifikasiIdentitasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
