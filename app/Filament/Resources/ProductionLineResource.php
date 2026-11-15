<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductionLineResource\Pages\CreateProductionLine;
use App\Filament\Resources\ProductionLineResource\Pages\EditProductionLine;
use App\Filament\Resources\ProductionLineResource\Pages\ListProductionLines;
use App\Models\ProductionLine;

class ProductionLineResource extends DenimResource
{
    protected static ?string $model = ProductionLine::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 300;

    protected static string $iconName = 'heroicon-o-bars-3';

    protected static string $groupName = 'Sewing';

    public static function getPages(): array
    {
        return [
            'index' => ListProductionLines::route('/'),
            'create' => CreateProductionLine::route('/create'),
            'edit' => EditProductionLine::route('/{record}/edit'),
        ];
    }
}
