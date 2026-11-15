<?php

namespace App\Filament\Resources;

use App\Filament\Resources\YarnLotResource\Pages\CreateYarnLot;
use App\Filament\Resources\YarnLotResource\Pages\EditYarnLot;
use App\Filament\Resources\YarnLotResource\Pages\ListYarnLots;
use App\Models\YarnLot;

class YarnLotResource extends DenimResource
{
    protected static ?string $model = YarnLot::class;

    protected static ?string $recordTitleAttribute = 'lot_no';

    protected static ?int $navigationSort = 110;

    protected static string $iconName = 'heroicon-o-sparkles';

    protected static string $groupName = 'Yarn';

    public static function getPages(): array
    {
        return [
            'index' => ListYarnLots::route('/'),
            'create' => CreateYarnLot::route('/create'),
            'edit' => EditYarnLot::route('/{record}/edit'),
        ];
    }
}
