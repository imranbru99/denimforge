<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BundleResource\Pages\CreateBundle;
use App\Filament\Resources\BundleResource\Pages\EditBundle;
use App\Filament\Resources\BundleResource\Pages\ListBundles;
use App\Models\Bundle;

class BundleResource extends DenimResource
{
    protected static ?string $model = Bundle::class;

    protected static ?string $recordTitleAttribute = 'code';

    protected static ?int $navigationSort = 290;

    protected static string $iconName = 'heroicon-o-qr-code';

    protected static string $groupName = 'Sewing';

    public static function getPages(): array
    {
        return [
            'index' => ListBundles::route('/'),
            'create' => CreateBundle::route('/create'),
            'edit' => EditBundle::route('/{record}/edit'),
        ];
    }
}
