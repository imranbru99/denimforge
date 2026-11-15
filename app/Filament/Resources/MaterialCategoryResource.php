<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialCategoryResource\Pages\CreateMaterialCategory;
use App\Filament\Resources\MaterialCategoryResource\Pages\EditMaterialCategory;
use App\Filament\Resources\MaterialCategoryResource\Pages\ListMaterialCategorys;
use App\Models\MaterialCategory;

class MaterialCategoryResource extends DenimResource
{
    protected static ?string $model = MaterialCategory::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 70;

    protected static string $iconName = 'heroicon-o-squares-2x2';

    protected static string $groupName = 'Inventory';

    public static function getPages(): array
    {
        return [
            'index' => ListMaterialCategorys::route('/'),
            'create' => CreateMaterialCategory::route('/create'),
            'edit' => EditMaterialCategory::route('/{record}/edit'),
        ];
    }
}
