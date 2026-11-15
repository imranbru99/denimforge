<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceOrderResource\Pages\CreateMaintenanceOrder;
use App\Filament\Resources\MaintenanceOrderResource\Pages\EditMaintenanceOrder;
use App\Filament\Resources\MaintenanceOrderResource\Pages\ListMaintenanceOrders;
use App\Models\MaintenanceOrder;

class MaintenanceOrderResource extends DenimResource
{
    protected static ?string $model = MaintenanceOrder::class;

    protected static ?string $recordTitleAttribute = 'number';

    protected static ?int $navigationSort = 380;

    protected static string $iconName = 'heroicon-o-wrench-screwdriver';

    protected static string $groupName = 'Maintenance';

    public static function getPages(): array
    {
        return [
            'index' => ListMaintenanceOrders::route('/'),
            'create' => CreateMaintenanceOrder::route('/create'),
            'edit' => EditMaintenanceOrder::route('/{record}/edit'),
        ];
    }
}
