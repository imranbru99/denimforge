<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InspectionResource\Pages\CreateInspection;
use App\Filament\Resources\InspectionResource\Pages\EditInspection;
use App\Filament\Resources\InspectionResource\Pages\ListInspections;
use App\Models\Inspection;

class InspectionResource extends DenimResource
{
    protected static ?string $model = Inspection::class;

    protected static ?string $recordTitleAttribute = 'inspection_no';

    protected static ?int $navigationSort = 340;

    protected static string $iconName = 'heroicon-o-shield-check';

    protected static string $groupName = 'Quality';

    public static function getPages(): array
    {
        return [
            'index' => ListInspections::route('/'),
            'create' => CreateInspection::route('/create'),
            'edit' => EditInspection::route('/{record}/edit'),
        ];
    }
}
