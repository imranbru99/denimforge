<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockMovementResource\Pages\CreateStockMovement;
use App\Filament\Resources\StockMovementResource\Pages\EditStockMovement;
use App\Filament\Resources\StockMovementResource\Pages\ListStockMovements;
use App\Models\StockMovement;

class StockMovementResource extends DenimResource
{
    protected static ?string $model = StockMovement::class;

    protected static ?string $recordTitleAttribute = 'reason';

    protected static ?int $navigationSort = 100;

    protected static string $iconName = 'heroicon-o-arrows-right-left';

    protected static string $groupName = 'Inventory';

    public static function getPages(): array
    {
        return [
            'index' => ListStockMovements::route('/'),
            'create' => CreateStockMovement::route('/create'),
            'edit' => EditStockMovement::route('/{record}/edit'),
        ];
    }
}
