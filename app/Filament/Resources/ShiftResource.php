<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShiftResource\Pages\CreateShift;
use App\Filament\Resources\ShiftResource\Pages\EditShift;
use App\Filament\Resources\ShiftResource\Pages\ListShifts;
use App\Models\Shift;

class ShiftResource extends DenimResource
{
    protected static ?string $model = Shift::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 40;

    protected static string $iconName = 'heroicon-o-clock';

    protected static string $groupName = 'People';

    public static function getPages(): array
    {
        return [
            'index' => ListShifts::route('/'),
            'create' => CreateShift::route('/create'),
            'edit' => EditShift::route('/{record}/edit'),
        ];
    }
}
