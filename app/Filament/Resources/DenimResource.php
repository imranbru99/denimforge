<?php

namespace App\Filament\Resources;

use App\Domain\Schema as MillSchema;
use App\Support\Modules;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Str;
use UnitEnum;

abstract class DenimResource extends Resource
{
    protected static string $iconName = 'heroicon-o-squares-2x2';

    protected static string $groupName = 'Factory';

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return static::$iconName;
    }

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return static::$groupName;
    }

    public static function canAccess(): bool
    {
        $entity = MillSchema::forModel(static::getModel());

        if (! Modules::enabled($entity['module'])) {
            return false;
        }

        return parent::canAccess();
    }

    public static function shouldRegisterNavigation(): bool
    {
        $entity = MillSchema::forModel(static::getModel());

        return Modules::enabled($entity['module']) && parent::shouldRegisterNavigation();
    }

    public static function form(Schema $schema): Schema
    {
        $entity = MillSchema::forModel(static::getModel());
        $fields = [];

        foreach ($entity['columns'] as $column) {
            if ($column['name'] === 'slug') {
                continue;
            }

            $field = match ($column['form']) {
                'textarea' => Textarea::make($column['name'])->rows(4)->columnSpanFull(),
                'json' => Textarea::make($column['name'])
                    ->rows(6)
                    ->columnSpanFull()
                    ->formatStateUsing(fn ($state) => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $state)
                    ->dehydrateStateUsing(function ($state) {
                        if (blank($state)) {
                            return null;
                        }

                        $decoded = json_decode((string) $state, true);

                        return json_last_error() === JSON_ERROR_NONE ? $decoded : ['raw' => $state];
                    }),
                'toggle' => Toggle::make($column['name'])->default((bool) ($column['default'] ?? false)),
                'number', 'decimal' => TextInput::make($column['name'])->numeric(),
                'date' => DatePicker::make($column['name']),
                'datetime' => DateTimePicker::make($column['name']),
                'select' => Select::make($column['name'])->options(self::options($column['options']))->native(false),
                'belongsTo' => Select::make($column['name'])
                    ->relationship($column['relation'], self::relatedLabel($column['related']))
                    ->searchable()
                    ->preload(),
                default => TextInput::make($column['name'])->maxLength(255),
            };

            if (empty($column['nullable']) && $column['form'] !== 'toggle') {
                $field->required();
            }

            if (! empty($column['has_default']) && $column['default'] !== null && ! in_array($column['form'], ['toggle', 'json'], true)) {
                $field->default($column['default']);
            }

            $fields[] = $field;
        }

        return $schema->components([
            Section::make(Str::headline(class_basename(static::getModel())))
                ->schema($fields)
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        $entity = MillSchema::forModel(static::getModel());
        $columns = [];
        $filters = [];
        $shown = 0;

        foreach ($entity['columns'] as $column) {
            if (in_array($column['form'], ['textarea', 'json'], true)) {
                continue;
            }

            if ($column['form'] === 'toggle') {
                $columns[] = IconColumn::make($column['name'])->boolean();
            } elseif ($shown < 6 || in_array($column['name'], ['status', 'result', 'severity'], true)) {
                $text = TextColumn::make($column['name'])->sortable();

                if (! empty($column['searchable'])) {
                    $text->searchable();
                }

                if ($column['form'] === 'select') {
                    $text->badge();
                }

                $columns[] = $text;
                $shown++;
            }

            if ($column['form'] === 'select' && in_array($column['name'], ['status', 'result', 'severity', 'stage', 'type', 'method'], true)) {
                $filters[] = SelectFilter::make($column['name'])->options(self::options($column['options']));
            }
        }

        $actions = $entity['lock'] ? [] : [EditAction::make(), DeleteAction::make()];
        $header = $entity['lock'] ? [] : [CreateAction::make()];
        $bulk = $entity['lock'] ? [] : [BulkActionGroup::make([DeleteBulkAction::make()])];

        return $table
            ->columns($columns)
            ->filters($filters)
            ->headerActions($header)
            ->recordActions($actions)
            ->toolbarActions($bulk)
            ->defaultSort('id', 'desc');
    }

    /**
     * @param  array<int, string>  $options
     * @return array<string, string>
     */
    protected static function options(array $options): array
    {
        $labels = [];

        foreach ($options as $option) {
            $labels[$option] = Str::headline(str_replace('_', ' ', $option));
        }

        return $labels;
    }

    protected static function relatedLabel(string $related): string
    {
        if ($related === 'User') {
            return 'name';
        }

        return MillSchema::forModel('App\\Models\\'.$related)['label'];
    }
}
