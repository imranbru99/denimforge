<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages\CreateMaterial;
use App\Filament\Resources\MaterialResource\Pages\EditMaterial;
use App\Filament\Resources\MaterialResource\Pages\ListMaterials;
use App\Models\Material;

class MaterialResource extends DenimResource
{
    protected static ?string $model = Material::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 80;

    protected static string $iconName = 'heroicon-o-cube';

    protected static string $groupName = 'Inventory';

    public static function getPages(): array
    {
        return [
            'index' => ListMaterials::route('/'),
            'create' => CreateMaterial::route('/create'),
            'edit' => EditMaterial::route('/{record}/edit'),
        ];
    }
}
