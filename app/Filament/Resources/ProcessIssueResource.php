<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProcessIssueResource\Pages\CreateProcessIssue;
use App\Filament\Resources\ProcessIssueResource\Pages\EditProcessIssue;
use App\Filament\Resources\ProcessIssueResource\Pages\ListProcessIssues;
use App\Models\ProcessIssue;

class ProcessIssueResource extends DenimResource
{
    protected static ?string $model = ProcessIssue::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 370;

    protected static string $iconName = 'heroicon-o-ticket';

    protected static string $groupName = 'Quality';

    public static function getPages(): array
    {
        return [
            'index' => ListProcessIssues::route('/'),
            'create' => CreateProcessIssue::route('/create'),
            'edit' => EditProcessIssue::route('/{record}/edit'),
        ];
    }
}
