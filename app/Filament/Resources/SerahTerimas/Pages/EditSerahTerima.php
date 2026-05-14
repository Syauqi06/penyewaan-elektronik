<?php

namespace App\Filament\Resources\SerahTerimas\Pages;

use App\Filament\Resources\SerahTerimas\SerahTerimaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSerahTerima extends EditRecord
{
    protected static string $resource = SerahTerimaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
