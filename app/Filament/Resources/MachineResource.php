<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MachineResource\Pages\CreateMachine;
use App\Filament\Resources\MachineResource\Pages\EditMachine;
use App\Filament\Resources\MachineResource\Pages\ListMachines;
use App\Models\Machine;

class MachineResource extends DenimResource
{
    protected static ?string $model = Machine::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 120;

    protected static string $iconName = 'heroicon-o-cpu-chip';

    protected static string $groupName = 'Maintenance';

    public static function getPages(): array
    {
        return [
            'index' => ListMachines::route('/'),
            'create' => CreateMachine::route('/create'),
            'edit' => EditMachine::route('/{record}/edit'),
        ];
    }
}
