<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryItemResource\Pages\CreateGalleryItem;
use App\Filament\Resources\GalleryItemResource\Pages\EditGalleryItem;
use App\Filament\Resources\GalleryItemResource\Pages\ListGalleryItems;
use App\Models\GalleryItem;

class GalleryItemResource extends DenimResource
{
    protected static ?string $model = GalleryItem::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 450;

    protected static string $iconName = 'heroicon-o-photo';

    protected static string $groupName = 'Website';

    public static function getPages(): array
    {
        return [
            'index' => ListGalleryItems::route('/'),
            'create' => CreateGalleryItem::route('/create'),
            'edit' => EditGalleryItem::route('/{record}/edit'),
        ];
    }
}
