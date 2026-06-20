<?php

namespace App\Filament\Resources\VerifikasiIdentitasResource\Pages;

use App\Filament\Resources\VerifikasiIdentitasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVerifikasiIdentitas extends EditRecord
{
    protected static string $resource = VerifikasiIdentitasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
