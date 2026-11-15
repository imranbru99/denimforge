<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlertResource\Pages\CreateAlert;
use App\Filament\Resources\AlertResource\Pages\EditAlert;
use App\Filament\Resources\AlertResource\Pages\ListAlerts;
use App\Models\Alert;

class AlertResource extends DenimResource
{
    protected static ?string $model = Alert::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 490;

    protected static string $iconName = 'heroicon-o-bell-alert';

    protected static string $groupName = 'Automation';

    public static function getPages(): array
    {
        return [
            'index' => ListAlerts::route('/'),
            'create' => CreateAlert::route('/create'),
            'edit' => EditAlert::route('/{record}/edit'),
        ];
    }
}
