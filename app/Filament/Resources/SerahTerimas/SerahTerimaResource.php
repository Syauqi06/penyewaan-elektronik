<?php

namespace App\Filament\Resources\SerahTerimas;

use App\Filament\Resources\SerahTerimas\Pages\CreateSerahTerima;
use App\Filament\Resources\SerahTerimas\Pages\EditSerahTerima;
use App\Filament\Resources\SerahTerimas\Pages\ListSerahTerimas;
use App\Filament\Resources\SerahTerimas\Schemas\SerahTerimaForm;
use App\Filament\Resources\SerahTerimas\Tables\SerahTerimasTable;
use App\Models\SerahTerima;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SerahTerimaResource extends Resource
{
    protected static ?string $model = SerahTerima::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SerahTerimaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SerahTerimasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSerahTerimas::route('/'),
            'create' => CreateSerahTerima::route('/create'),
            'edit' => EditSerahTerima::route('/{record}/edit'),
        ];
    }
}
