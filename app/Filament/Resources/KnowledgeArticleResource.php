<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KnowledgeArticleResource\Pages\CreateKnowledgeArticle;
use App\Filament\Resources\KnowledgeArticleResource\Pages\EditKnowledgeArticle;
use App\Filament\Resources\KnowledgeArticleResource\Pages\ListKnowledgeArticles;
use App\Models\KnowledgeArticle;

class KnowledgeArticleResource extends DenimResource
{
    protected static ?string $model = KnowledgeArticle::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?int $navigationSort = 360;

    protected static string $iconName = 'heroicon-o-book-open';

    protected static string $groupName = 'Knowledge';

    public static function getPages(): array
    {
        return [
            'index' => ListKnowledgeArticles::route('/'),
            'create' => CreateKnowledgeArticle::route('/create'),
            'edit' => EditKnowledgeArticle::route('/{record}/edit'),
        ];
    }
}
