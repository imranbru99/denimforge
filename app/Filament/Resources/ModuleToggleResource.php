<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModuleToggleResource\Pages\CreateModuleToggle;
use App\Filament\Resources\ModuleToggleResource\Pages\EditModuleToggle;
use App\Filament\Resources\ModuleToggleResource\Pages\ListModuleToggles;
use App\Models\ModuleToggle;

class ModuleToggleResource extends DenimResource
{
    protected static ?string $model = ModuleToggle::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 500;

    protected static string $iconName = 'heroicon-o-adjustments-horizontal';

    protected static string $groupName = 'System';

    public static function getPages(): array
    {
        return [
            'index' => ListModuleToggles::route('/'),
            'create' => CreateModuleToggle::route('/create'),
            'edit' => EditModuleToggle::route('/{record}/edit'),
        ];
    }
}
