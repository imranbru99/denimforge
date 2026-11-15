<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InspectionDefectResource\Pages\CreateInspectionDefect;
use App\Filament\Resources\InspectionDefectResource\Pages\EditInspectionDefect;
use App\Filament\Resources\InspectionDefectResource\Pages\ListInspectionDefects;
use App\Models\InspectionDefect;

class InspectionDefectResource extends DenimResource
{
    protected static ?string $model = InspectionDefect::class;

    protected static ?string $recordTitleAttribute = 'defect_code';

    protected static ?int $navigationSort = 350;

    protected static string $iconName = 'heroicon-o-exclamation-triangle';

    protected static string $groupName = 'Quality';

    public static function getPages(): array
    {
        return [
            'index' => ListInspectionDefects::route('/'),
            'create' => CreateInspectionDefect::route('/create'),
            'edit' => EditInspectionDefect::route('/{record}/edit'),
        ];
    }
}
