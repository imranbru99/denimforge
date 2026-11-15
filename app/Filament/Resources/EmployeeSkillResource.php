<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeSkillResource\Pages\CreateEmployeeSkill;
use App\Filament\Resources\EmployeeSkillResource\Pages\EditEmployeeSkill;
use App\Filament\Resources\EmployeeSkillResource\Pages\ListEmployeeSkills;
use App\Models\EmployeeSkill;

class EmployeeSkillResource extends DenimResource
{
    protected static ?string $model = EmployeeSkill::class;

    protected static ?string $recordTitleAttribute = 'skill';

    protected static ?int $navigationSort = 60;

    protected static string $iconName = 'heroicon-o-academic-cap';

    protected static string $groupName = 'People';

    public static function getPages(): array
    {
        return [
            'index' => ListEmployeeSkills::route('/'),
            'create' => CreateEmployeeSkill::route('/create'),
            'edit' => EditEmployeeSkill::route('/{record}/edit'),
        ];
    }
}
