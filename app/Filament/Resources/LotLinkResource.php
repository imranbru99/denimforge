<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LotLinkResource\Pages\CreateLotLink;
use App\Filament\Resources\LotLinkResource\Pages\EditLotLink;
use App\Filament\Resources\LotLinkResource\Pages\ListLotLinks;
use App\Models\LotLink;

class LotLinkResource extends DenimResource
{
    protected static ?string $model = LotLink::class;

    protected static ?string $recordTitleAttribute = 'uom';

    protected static ?int $navigationSort = 430;

    protected static string $iconName = 'heroicon-o-link';

    protected static string $groupName = 'Traceability';

    public static function getPages(): array
    {
        return [
            'index' => ListLotLinks::route('/'),
            'create' => CreateLotLink::route('/create'),
            'edit' => EditLotLink::route('/{record}/edit'),
        ];
    }
}
