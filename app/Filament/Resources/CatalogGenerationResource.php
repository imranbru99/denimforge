<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CatalogGenerationResource\Pages\ListCatalogGenerations;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use ImranDevBd\AiCatalog\Models\CatalogGeneration;

class CatalogGenerationResource extends Resource
{
    protected static ?string $model = CatalogGeneration::class;

    protected static ?string $navigationLabel = 'Catalog drafts';

    protected static ?int $navigationSort = 40;

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-sparkles';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Catalog';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super-admin', 'factory-manager']) ?? false;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')->limit(8),
                TextColumn::make('status')->badge(),
                TextColumn::make('payload.title')->label('Title')->limit(40),
                TextColumn::make('overall_confidence'),
                TextColumn::make('created_at')->dateTime(),
            ])
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCatalogGenerations::route('/'),
        ];
    }
}
