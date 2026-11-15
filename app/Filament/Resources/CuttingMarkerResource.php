<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CuttingMarkerResource\Pages\CreateCuttingMarker;
use App\Filament\Resources\CuttingMarkerResource\Pages\EditCuttingMarker;
use App\Filament\Resources\CuttingMarkerResource\Pages\ListCuttingMarkers;
use App\Models\CuttingMarker;

class CuttingMarkerResource extends DenimResource
{
    protected static ?string $model = CuttingMarker::class;

    protected static ?string $recordTitleAttribute = 'marker_no';

    protected static ?int $navigationSort = 280;

    protected static string $iconName = 'heroicon-o-scissors';

    protected static string $groupName = 'Cutting';

    public static function getPages(): array
    {
        return [
            'index' => ListCuttingMarkers::route('/'),
            'create' => CreateCuttingMarker::route('/create'),
            'edit' => EditCuttingMarker::route('/{record}/edit'),
        ];
    }
}
