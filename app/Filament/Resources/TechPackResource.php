<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TechPackResource\Pages\CreateTechPack;
use App\Filament\Resources\TechPackResource\Pages\EditTechPack;
use App\Filament\Resources\TechPackResource\Pages\ListTechPacks;
use App\Models\TechPack;

class TechPackResource extends DenimResource
{
    protected static ?string $model = TechPack::class;

    protected static ?string $recordTitleAttribute = 'version';

    protected static ?int $navigationSort = 150;

    protected static string $iconName = 'heroicon-o-document-text';

    protected static string $groupName = 'Planning';

    public static function getPages(): array
    {
        return [
            'index' => ListTechPacks::route('/'),
            'create' => CreateTechPack::route('/create'),
            'edit' => EditTechPack::route('/{record}/edit'),
        ];
    }
}
