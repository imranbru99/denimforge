<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SampleResource\Pages\CreateSample;
use App\Filament\Resources\SampleResource\Pages\EditSample;
use App\Filament\Resources\SampleResource\Pages\ListSamples;
use App\Models\Sample;

class SampleResource extends DenimResource
{
    protected static ?string $model = Sample::class;

    protected static ?string $recordTitleAttribute = 'sample_no';

    protected static ?int $navigationSort = 190;

    protected static string $iconName = 'heroicon-o-beaker';

    protected static string $groupName = 'Planning';

    public static function getPages(): array
    {
        return [
            'index' => ListSamples::route('/'),
            'create' => CreateSample::route('/create'),
            'edit' => EditSample::route('/{record}/edit'),
        ];
    }
}
