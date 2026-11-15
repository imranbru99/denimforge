<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BuyerResource\Pages\CreateBuyer;
use App\Filament\Resources\BuyerResource\Pages\EditBuyer;
use App\Filament\Resources\BuyerResource\Pages\ListBuyers;
use App\Models\Buyer;

class BuyerResource extends DenimResource
{
    protected static ?string $model = Buyer::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 10;

    protected static string $iconName = 'heroicon-o-building-storefront';

    protected static string $groupName = 'Commercial';

    public static function getPages(): array
    {
        return [
            'index' => ListBuyers::route('/'),
            'create' => CreateBuyer::route('/create'),
            'edit' => EditBuyer::route('/{record}/edit'),
        ];
    }
}
