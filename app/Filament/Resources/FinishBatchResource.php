<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FinishBatchResource\Pages\CreateFinishBatch;
use App\Filament\Resources\FinishBatchResource\Pages\EditFinishBatch;
use App\Filament\Resources\FinishBatchResource\Pages\ListFinishBatchs;
use App\Models\FinishBatch;

class FinishBatchResource extends DenimResource
{
    protected static ?string $model = FinishBatch::class;

    protected static ?string $recordTitleAttribute = 'batch_no';

    protected static ?int $navigationSort = 260;

    protected static string $iconName = 'heroicon-o-sparkles';

    protected static string $groupName = 'Finishing';

    public static function getPages(): array
    {
        return [
            'index' => ListFinishBatchs::route('/'),
            'create' => CreateFinishBatch::route('/create'),
            'edit' => EditFinishBatch::route('/{record}/edit'),
        ];
    }
}
