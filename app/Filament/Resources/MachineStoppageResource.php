<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MachineStoppageResource\Pages\CreateMachineStoppage;
use App\Filament\Resources\MachineStoppageResource\Pages\EditMachineStoppage;
use App\Filament\Resources\MachineStoppageResource\Pages\ListMachineStoppages;
use App\Models\MachineStoppage;

class MachineStoppageResource extends DenimResource
{
    protected static ?string $model = MachineStoppage::class;

    protected static ?string $recordTitleAttribute = 'reason';

    protected static ?int $navigationSort = 270;

    protected static string $iconName = 'heroicon-o-pause-circle';

    protected static string $groupName = 'Maintenance';

    public static function getPages(): array
    {
        return [
            'index' => ListMachineStoppages::route('/'),
            'create' => CreateMachineStoppage::route('/create'),
            'edit' => EditMachineStoppage::route('/{record}/edit'),
        ];
    }
}
