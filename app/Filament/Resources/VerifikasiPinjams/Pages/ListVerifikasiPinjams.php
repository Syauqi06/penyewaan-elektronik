<?php

namespace App\Filament\Resources\VerifikasiPinjams\Pages;

use App\Filament\Resources\VerifikasiPinjams\VerifikasiPinjamResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVerifikasiPinjams extends ListRecords
{
    protected static string $resource = VerifikasiPinjamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
