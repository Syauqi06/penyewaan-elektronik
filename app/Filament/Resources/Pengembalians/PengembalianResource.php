<?php

namespace App\Filament\Resources\Pengembalians;

use App\Filament\Resources\Pengembalians\Pages\CreatePengembalian;
use App\Filament\Resources\Pengembalians\Pages\EditPengembalian;
use App\Filament\Resources\Pengembalians\Pages\ListPengembalians;
use App\Filament\Resources\Pengembalians\Schemas\PengembalianForm;
use App\Filament\Resources\Pengembalians\Tables\PengembaliansTable;
use App\Models\Pengembalian;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PengembalianResource extends Resource
{
    protected static ?string $model = Pengembalian::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PengembalianForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PengembaliansTable::configure($table);
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
            'index' => ListPengembalians::route('/'),
            'create' => CreatePengembalian::route('/create'),
            'edit' => EditPengembalian::route('/{record}/edit'),
        ];
    }
}
