<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StyleResource\Pages\CreateStyle;
use App\Filament\Resources\StyleResource\Pages\EditStyle;
use App\Filament\Resources\StyleResource\Pages\ListStyles;
use App\Models\Style;

class StyleResource extends DenimResource
{
    protected static ?string $model = Style::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 140;

    protected static string $iconName = 'heroicon-o-tag';

    protected static string $groupName = 'Planning';

    public static function getPages(): array
    {
        return [
            'index' => ListStyles::route('/'),
            'create' => CreateStyle::route('/create'),
            'edit' => EditStyle::route('/{record}/edit'),
        ];
    }
}
