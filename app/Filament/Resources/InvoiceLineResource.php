<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceLineResource\Pages\CreateInvoiceLine;
use App\Filament\Resources\InvoiceLineResource\Pages\EditInvoiceLine;
use App\Filament\Resources\InvoiceLineResource\Pages\ListInvoiceLines;
use App\Models\InvoiceLine;

class InvoiceLineResource extends DenimResource
{
    protected static ?string $model = InvoiceLine::class;

    protected static ?string $recordTitleAttribute = 'description';

    protected static ?int $navigationSort = 410;

    protected static string $iconName = 'heroicon-o-list-bullet';

    protected static string $groupName = 'Finance';

    public static function getPages(): array
    {
        return [
            'index' => ListInvoiceLines::route('/'),
            'create' => CreateInvoiceLine::route('/create'),
            'edit' => EditInvoiceLine::route('/{record}/edit'),
        ];
    }
}
