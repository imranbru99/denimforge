<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WashRecipeResource\Pages\CreateWashRecipe;
use App\Filament\Resources\WashRecipeResource\Pages\EditWashRecipe;
use App\Filament\Resources\WashRecipeResource\Pages\ListWashRecipes;
use App\Models\WashRecipe;

class WashRecipeResource extends DenimResource
{
    protected static ?string $model = WashRecipe::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 320;

    protected static string $iconName = 'heroicon-o-fire';

    protected static string $groupName = 'Washing';

    public static function getPages(): array
    {
        return [
            'index' => ListWashRecipes::route('/'),
            'create' => CreateWashRecipe::route('/create'),
            'edit' => EditWashRecipe::route('/{record}/edit'),
        ];
    }
}
