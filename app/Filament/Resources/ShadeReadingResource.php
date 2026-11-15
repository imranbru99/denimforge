<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShadeReadingResource\Pages\CreateShadeReading;
use App\Filament\Resources\ShadeReadingResource\Pages\EditShadeReading;
use App\Filament\Resources\ShadeReadingResource\Pages\ListShadeReadings;
use App\Models\ShadeReading;

class ShadeReadingResource extends DenimResource
{
    protected static ?string $model = ShadeReading::class;

    protected static ?string $recordTitleAttribute = 'position';

    protected static ?int $navigationSort = 230;

    protected static string $iconName = 'heroicon-o-sun';

    protected static string $groupName = 'Dyeing';

    public static function getPages(): array
    {
        return [
            'index' => ListShadeReadings::route('/'),
            'create' => CreateShadeReading::route('/create'),
            'edit' => EditShadeReading::route('/{record}/edit'),
        ];
    }
}
