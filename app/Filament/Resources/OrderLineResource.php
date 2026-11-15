<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderLineResource\Pages\CreateOrderLine;
use App\Filament\Resources\OrderLineResource\Pages\EditOrderLine;
use App\Filament\Resources\OrderLineResource\Pages\ListOrderLines;
use App\Models\OrderLine;

class OrderLineResource extends DenimResource
{
    protected static ?string $model = OrderLine::class;

    protected static ?string $recordTitleAttribute = 'color';

    protected static ?int $navigationSort = 180;

    protected static string $iconName = 'heroicon-o-queue-list';

    protected static string $groupName = 'Planning';

    public static function getPages(): array
    {
        return [
            'index' => ListOrderLines::route('/'),
            'create' => CreateOrderLine::route('/create'),
            'edit' => EditOrderLine::route('/{record}/edit'),
        ];
    }
}
