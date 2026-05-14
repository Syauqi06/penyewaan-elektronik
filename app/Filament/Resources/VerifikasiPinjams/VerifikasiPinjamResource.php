<?php

namespace App\Filament\Resources\VerifikasiPinjams;

use App\Filament\Resources\VerifikasiPinjams\Pages\CreateVerifikasiPinjam;
use App\Filament\Resources\VerifikasiPinjams\Pages\EditVerifikasiPinjam;
use App\Filament\Resources\VerifikasiPinjams\Pages\ListVerifikasiPinjams;
use App\Filament\Resources\VerifikasiPinjams\Schemas\VerifikasiPinjamForm;
use App\Filament\Resources\VerifikasiPinjams\Tables\VerifikasiPinjamsTable;
use App\Models\VerifikasiPinjam;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VerifikasiPinjamResource extends Resource
{
    protected static ?string $model = VerifikasiPinjam::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return VerifikasiPinjamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VerifikasiPinjamsTable::configure($table);
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
            'index' => ListVerifikasiPinjams::route('/'),
            'create' => CreateVerifikasiPinjam::route('/create'),
            'edit' => EditVerifikasiPinjam::route('/{record}/edit'),
        ];
    }
}
