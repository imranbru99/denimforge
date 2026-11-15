<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WeaveDefectResource\Pages\CreateWeaveDefect;
use App\Filament\Resources\WeaveDefectResource\Pages\EditWeaveDefect;
use App\Filament\Resources\WeaveDefectResource\Pages\ListWeaveDefects;
use App\Models\WeaveDefect;

class WeaveDefectResource extends DenimResource
{
    protected static ?string $model = WeaveDefect::class;

    protected static ?string $recordTitleAttribute = 'defect';

    protected static ?int $navigationSort = 250;

    protected static string $iconName = 'heroicon-o-exclamation-triangle';

    protected static string $groupName = 'Weaving';

    public static function getPages(): array
    {
        return [
            'index' => ListWeaveDefects::route('/'),
            'create' => CreateWeaveDefect::route('/create'),
            'edit' => EditWeaveDefect::route('/{record}/edit'),
        ];
    }
}
