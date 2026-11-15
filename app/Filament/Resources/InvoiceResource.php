<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages\CreateInvoice;
use App\Filament\Resources\InvoiceResource\Pages\EditInvoice;
use App\Filament\Resources\InvoiceResource\Pages\ListInvoices;
use App\Models\Invoice;

class InvoiceResource extends DenimResource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $recordTitleAttribute = 'number';

    protected static ?int $navigationSort = 400;

    protected static string $iconName = 'heroicon-o-document-currency-dollar';

    protected static string $groupName = 'Finance';

    public static function getPages(): array
    {
        return [
            'index' => ListInvoices::route('/'),
            'create' => CreateInvoice::route('/create'),
            'edit' => EditInvoice::route('/{record}/edit'),
        ];
    }
}
