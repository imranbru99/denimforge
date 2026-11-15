<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobOpeningResource\Pages\CreateJobOpening;
use App\Filament\Resources\JobOpeningResource\Pages\EditJobOpening;
use App\Filament\Resources\JobOpeningResource\Pages\ListJobOpenings;
use App\Models\JobOpening;

class JobOpeningResource extends DenimResource
{
    protected static ?string $model = JobOpening::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 470;

    protected static string $iconName = 'heroicon-o-briefcase';

    protected static string $groupName = 'Website';

    public static function getPages(): array
    {
        return [
            'index' => ListJobOpenings::route('/'),
            'create' => CreateJobOpening::route('/create'),
            'edit' => EditJobOpening::route('/{record}/edit'),
        ];
    }
}
