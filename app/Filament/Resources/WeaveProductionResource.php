<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeaveProductionResource\Pages\CreateWeaveProduction;
use App\Filament\Resources\WeaveProductionResource\Pages\EditWeaveProduction;
use App\Filament\Resources\WeaveProductionResource\Pages\ListWeaveProductions;
use App\Models\WeaveProduction;

class WeaveProductionResource extends DenimResource
{
    protected static ?string $model = WeaveProduction::class;

    protected static ?string $recordTitleAttribute = 'roll_no';

    protected static ?int $navigationSort = 240;

    protected static string $iconName = 'heroicon-o-view-columns';

    protected static string $groupName = 'Weaving';

    public static function getPages(): array
    {
        return [
            'index' => ListWeaveProductions::route('/'),
            'create' => CreateWeaveProduction::route('/create'),
            'edit' => EditWeaveProduction::route('/{record}/edit'),
        ];
    }
}
