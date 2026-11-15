<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StyleCostingResource\Pages\CreateStyleCosting;
use App\Filament\Resources\StyleCostingResource\Pages\EditStyleCosting;
use App\Filament\Resources\StyleCostingResource\Pages\ListStyleCostings;
use App\Models\StyleCosting;

class StyleCostingResource extends DenimResource
{
    protected static ?string $model = StyleCosting::class;

    protected static ?string $recordTitleAttribute = 'currency';

    protected static ?int $navigationSort = 390;

    protected static string $iconName = 'heroicon-o-calculator';

    protected static string $groupName = 'Finance';

    public static function getPages(): array
    {
        return [
            'index' => ListStyleCostings::route('/'),
            'create' => CreateStyleCosting::route('/create'),
            'edit' => EditStyleCosting::route('/{record}/edit'),
        ];
    }
}
