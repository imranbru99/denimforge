<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WorkOrderResource\Pages\CreateWorkOrder;
use App\Filament\Resources\WorkOrderResource\Pages\EditWorkOrder;
use App\Filament\Resources\WorkOrderResource\Pages\ListWorkOrders;
use App\Models\WorkOrder;

class WorkOrderResource extends DenimResource
{
    protected static ?string $model = WorkOrder::class;

    protected static ?string $recordTitleAttribute = 'number';

    protected static ?int $navigationSort = 200;

    protected static string $iconName = 'heroicon-o-clipboard-document-check';

    protected static string $groupName = 'Planning';

    public static function getPages(): array
    {
        return [
            'index' => ListWorkOrders::route('/'),
            'create' => CreateWorkOrder::route('/create'),
            'edit' => EditWorkOrder::route('/{record}/edit'),
        ];
    }
}
