<?php

namespace App\Filament\Resources\VerifikasiPinjams\Pages;

use App\Filament\Resources\VerifikasiPinjams\VerifikasiPinjamResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVerifikasiPinjam extends EditRecord
{
    protected static string $resource = VerifikasiPinjamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
