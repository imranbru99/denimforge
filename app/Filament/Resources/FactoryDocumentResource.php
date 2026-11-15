<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FactoryDocumentResource\Pages\CreateFactoryDocument;
use App\Filament\Resources\FactoryDocumentResource\Pages\EditFactoryDocument;
use App\Filament\Resources\FactoryDocumentResource\Pages\ListFactoryDocuments;
use App\Models\FactoryDocument;

class FactoryDocumentResource extends DenimResource
{
    protected static ?string $model = FactoryDocument::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 480;

    protected static string $iconName = 'heroicon-o-document-magnifying-glass';

    protected static string $groupName = 'Documents';

    public static function getPages(): array
    {
        return [
            'index' => ListFactoryDocuments::route('/'),
            'create' => CreateFactoryDocument::route('/create'),
            'edit' => EditFactoryDocument::route('/{record}/edit'),
        ];
    }
}
