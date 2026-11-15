<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BuyerOrderResource\Pages\CreateBuyerOrder;
use App\Filament\Resources\BuyerOrderResource\Pages\EditBuyerOrder;
use App\Filament\Resources\BuyerOrderResource\Pages\ListBuyerOrders;
use App\Models\BuyerOrder;

class BuyerOrderResource extends DenimResource
{
    protected static ?string $model = BuyerOrder::class;

    protected static ?string $recordTitleAttribute = 'order_no';

    protected static ?int $navigationSort = 170;

    protected static string $iconName = 'heroicon-o-clipboard-document-list';

    protected static string $groupName = 'Planning';

    public static function getPages(): array
    {
        return [
            'index' => ListBuyerOrders::route('/'),
            'create' => CreateBuyerOrder::route('/create'),
            'edit' => EditBuyerOrder::route('/{record}/edit'),
        ];
    }
}
