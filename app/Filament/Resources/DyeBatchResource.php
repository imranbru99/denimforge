<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DyeBatchResource\Pages\CreateDyeBatch;
use App\Filament\Resources\DyeBatchResource\Pages\EditDyeBatch;
use App\Filament\Resources\DyeBatchResource\Pages\ListDyeBatchs;
use App\Models\DyeBatch;

class DyeBatchResource extends DenimResource
{
    protected static ?string $model = DyeBatch::class;

    protected static ?string $recordTitleAttribute = 'batch_no';

    protected static ?int $navigationSort = 210;

    protected static string $iconName = 'heroicon-o-beaker';

    protected static string $groupName = 'Dyeing';

    public static function getPages(): array
    {
        return [
            'index' => ListDyeBatchs::route('/'),
            'create' => CreateDyeBatch::route('/create'),
            'edit' => EditDyeBatch::route('/{record}/edit'),
        ];
    }
}
