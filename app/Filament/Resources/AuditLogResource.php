<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditLogResource\Pages\CreateAuditLog;
use App\Filament\Resources\AuditLogResource\Pages\EditAuditLog;
use App\Filament\Resources\AuditLogResource\Pages\ListAuditLogs;
use App\Models\AuditLog;

class AuditLogResource extends DenimResource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $recordTitleAttribute = 'event';

    protected static ?int $navigationSort = 520;

    protected static string $iconName = 'heroicon-o-clipboard-document-check';

    protected static string $groupName = 'System';

    public static function getPages(): array
    {
        return [
            'index' => ListAuditLogs::route('/'),
            'create' => CreateAuditLog::route('/create'),
            'edit' => EditAuditLog::route('/{record}/edit'),
        ];
    }
}
