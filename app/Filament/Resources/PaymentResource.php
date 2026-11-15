<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages\CreatePayment;
use App\Filament\Resources\PaymentResource\Pages\EditPayment;
use App\Filament\Resources\PaymentResource\Pages\ListPayments;
use App\Models\Payment;

class PaymentResource extends DenimResource
{
    protected static ?string $model = Payment::class;

    protected static ?string $recordTitleAttribute = 'reference';

    protected static ?int $navigationSort = 420;

    protected static string $iconName = 'heroicon-o-banknotes';

    protected static string $groupName = 'Finance';

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'edit' => EditPayment::route('/{record}/edit'),
        ];
    }
}
