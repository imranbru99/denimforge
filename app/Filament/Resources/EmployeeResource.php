<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages\CreateEmployee;
use App\Filament\Resources\EmployeeResource\Pages\EditEmployee;
use App\Filament\Resources\EmployeeResource\Pages\ListEmployees;
use App\Models\Employee;

class EmployeeResource extends DenimResource
{
    protected static ?string $model = Employee::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 50;

    protected static string $iconName = 'heroicon-o-users';

    protected static string $groupName = 'People';

    public static function getPages(): array
    {
        return [
            'index' => ListEmployees::route('/'),
            'create' => CreateEmployee::route('/create'),
            'edit' => EditEmployee::route('/{record}/edit'),
        ];
    }
}
