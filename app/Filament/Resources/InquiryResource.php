<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InquiryResource\Pages\CreateInquiry;
use App\Filament\Resources\InquiryResource\Pages\EditInquiry;
use App\Filament\Resources\InquiryResource\Pages\ListInquirys;
use App\Models\Inquiry;

class InquiryResource extends DenimResource
{
    protected static ?string $model = Inquiry::class;

    protected static ?string $recordTitleAttribute = 'subject';

    protected static ?int $navigationSort = 460;

    protected static string $iconName = 'heroicon-o-envelope';

    protected static string $groupName = 'Website';

    public static function getPages(): array
    {
        return [
            'index' => ListInquirys::route('/'),
            'create' => CreateInquiry::route('/create'),
            'edit' => EditInquiry::route('/{record}/edit'),
        ];
    }
}
