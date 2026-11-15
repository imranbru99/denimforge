<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryLotResource\Pages\CreateInventoryLot;
use App\Filament\Resources\InventoryLotResource\Pages\EditInventoryLot;
use App\Filament\Resources\InventoryLotResource\Pages\ListInventoryLots;
use App\Models\InventoryLot;

class InventoryLotResource extends DenimResource
{
    protected static ?string $model = InventoryLot::class;

    protected static ?string $recordTitleAttribute = 'lot_no';

    protected static ?int $navigationSort = 90;

    protected static string $iconName = 'heroicon-o-archive-box';

    protected static string $groupName = 'Inventory';

    public static function getPages(): array
    {
        return [
            'index' => ListInventoryLots::route('/'),
            'create' => CreateInventoryLot::route('/create'),
            'edit' => EditInventoryLot::route('/{record}/edit'),
        ];
    }
}
