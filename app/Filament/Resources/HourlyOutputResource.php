<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HourlyOutputResource\Pages\CreateHourlyOutput;
use App\Filament\Resources\HourlyOutputResource\Pages\EditHourlyOutput;
use App\Filament\Resources\HourlyOutputResource\Pages\ListHourlyOutputs;
use App\Models\HourlyOutput;

class HourlyOutputResource extends DenimResource
{
    protected static ?string $model = HourlyOutput::class;

    protected static ?string $recordTitleAttribute = 'hour_start';

    protected static ?int $navigationSort = 310;

    protected static string $iconName = 'heroicon-o-chart-bar';

    protected static string $groupName = 'Sewing';

    public static function getPages(): array
    {
        return [
            'index' => ListHourlyOutputs::route('/'),
            'create' => CreateHourlyOutput::route('/create'),
            'edit' => EditHourlyOutput::route('/{record}/edit'),
        ];
    }
}
