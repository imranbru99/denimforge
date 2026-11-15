<?php

namespace App\Filament\Resources\InvoiceLineResource\Pages;

use App\Filament\Resources\InvoiceLineResource;
use Filament\Resources\Pages\ListRecords;

class ListInvoiceLines extends ListRecords
{
    protected static string $resource = InvoiceLineResource::class;
}
