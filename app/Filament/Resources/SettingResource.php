<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SettingResource\Pages\CreateSetting;
use App\Filament\Resources\SettingResource\Pages\EditSetting;
use App\Filament\Resources\SettingResource\Pages\ListSettings;
use App\Models\Setting;

class SettingResource extends DenimResource
{
    protected static ?string $model = Setting::class;

    protected static ?string $recordTitleAttribute = 'key';

    protected static ?int $navigationSort = 510;

    protected static string $iconName = 'heroicon-o-cog-6-tooth';

    protected static string $groupName = 'System';

    public static function getPages(): array
    {
        return [
            'index' => ListSettings::route('/'),
            'create' => CreateSetting::route('/create'),
            'edit' => EditSetting::route('/{record}/edit'),
        ];
    }
}
