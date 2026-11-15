<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShadeCardResource\Pages\CreateShadeCard;
use App\Filament\Resources\ShadeCardResource\Pages\EditShadeCard;
use App\Filament\Resources\ShadeCardResource\Pages\ListShadeCards;
use App\Models\ShadeCard;

class ShadeCardResource extends DenimResource
{
    protected static ?string $model = ShadeCard::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 160;

    protected static string $iconName = 'heroicon-o-eye-dropper';

    protected static string $groupName = 'Dyeing';

    public static function getPages(): array
    {
        return [
            'index' => ListShadeCards::route('/'),
            'create' => CreateShadeCard::route('/create'),
            'edit' => EditShadeCard::route('/{record}/edit'),
        ];
    }
}
