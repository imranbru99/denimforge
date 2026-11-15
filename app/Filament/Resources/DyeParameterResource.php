<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DyeParameterResource\Pages\CreateDyeParameter;
use App\Filament\Resources\DyeParameterResource\Pages\EditDyeParameter;
use App\Filament\Resources\DyeParameterResource\Pages\ListDyeParameters;
use App\Models\DyeParameter;

class DyeParameterResource extends DenimResource
{
    protected static ?string $model = DyeParameter::class;

    protected static ?string $recordTitleAttribute = 'station';

    protected static ?int $navigationSort = 220;

    protected static string $iconName = 'heroicon-o-variable';

    protected static string $groupName = 'Dyeing';

    public static function getPages(): array
    {
        return [
            'index' => ListDyeParameters::route('/'),
            'create' => CreateDyeParameter::route('/create'),
            'edit' => EditDyeParameter::route('/{record}/edit'),
        ];
    }
}
