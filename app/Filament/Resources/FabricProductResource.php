<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FabricProductResource\Pages\CreateFabricProduct;
use App\Filament\Resources\FabricProductResource\Pages\EditFabricProduct;
use App\Filament\Resources\FabricProductResource\Pages\ListFabricProducts;
use App\Models\FabricProduct;

class FabricProductResource extends DenimResource
{
    protected static ?string $model = FabricProduct::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 130;

    protected static string $iconName = 'heroicon-o-swatch';

    protected static string $groupName = 'Catalog';

    public static function getPages(): array
    {
        return [
            'index' => ListFabricProducts::route('/'),
            'create' => CreateFabricProduct::route('/create'),
            'edit' => EditFabricProduct::route('/{record}/edit'),
        ];
    }
}
