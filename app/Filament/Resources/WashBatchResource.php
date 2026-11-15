<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WashBatchResource\Pages\CreateWashBatch;
use App\Filament\Resources\WashBatchResource\Pages\EditWashBatch;
use App\Filament\Resources\WashBatchResource\Pages\ListWashBatchs;
use App\Models\WashBatch;

class WashBatchResource extends DenimResource
{
    protected static ?string $model = WashBatch::class;

    protected static ?string $recordTitleAttribute = 'batch_no';

    protected static ?int $navigationSort = 330;

    protected static string $iconName = 'heroicon-o-beaker';

    protected static string $groupName = 'Washing';

    public static function getPages(): array
    {
        return [
            'index' => ListWashBatchs::route('/'),
            'create' => CreateWashBatch::route('/create'),
            'edit' => EditWashBatch::route('/{record}/edit'),
        ];
    }
}
