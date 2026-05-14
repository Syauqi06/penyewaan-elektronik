<?php

namespace App\Filament\Resources\SerahTerimas\Pages;

use App\Filament\Resources\SerahTerimas\SerahTerimaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSerahTerimas extends ListRecords
{
    protected static string $resource = SerahTerimaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
