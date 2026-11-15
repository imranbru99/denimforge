<?php

use App\Domain\Schema;

require __DIR__.'/../vendor/autoload.php';

$entities = Schema::all();
$root = dirname(__DIR__);

writeMigration($root, $entities);

foreach ($entities as $entity) {
    writeModel($root, $entity);
    writePolicy($root, $entity);
    writeResource($root, $entity);
}

echo 'Generated '.count($entities)." entities\n";

function writeMigration(string $root, array $entities): void
{
    $body = '';

    foreach ($entities as $entity) {
        $lines = [];

        foreach ($entity['columns'] as $column) {
            $lines[] = migrationColumn($column);
        }

        $inner = implode("\n", $lines);
        $body .= <<<PHP
        Schema::create('{$entity['table']}', function (Blueprint \$table) {
            \$table->id();
{$inner}
            \$table->timestamps();
        });


PHP;
    }

    $body .= <<<'PHP'
        Schema::table('users', function (Blueprint $table) {
            $table->string('locale')->default('en');
            $table->boolean('is_active')->default(true);
            $table->foreignId('buyer_id')->nullable()->constrained('buyers')->nullOnDelete();
        });

        Schema::create('slug_histories', function (Blueprint $table) {
            $table->id();
            $table->morphs('sluggable');
            $table->string('slug')->index();
            $table->string('field')->default('slug');
            $table->timestamps();
            $table->unique(['sluggable_type', 'slug', 'field'], 'slug_histories_unique');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

PHP;

    $drops = '';

    foreach (array_reverse($entities) as $entity) {
        $drops .= "        Schema::dropIfExists('{$entity['table']}');\n";
    }

    $php = <<<PHP
<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
{$body}    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint \$table) {
            \$table->dropConstrainedForeignId('buyer_id');
            \$table->dropColumn(['locale', 'is_active']);
        });

        Schema::dropIfExists('notifications');
        Schema::dropIfExists('slug_histories');
{$drops}    }
};

PHP;

    file_put_contents($root.'/database/migrations/2026_11_16_100000_create_denimforge_tables.php', $php);
}

function migrationColumn(array $column): string
{
    $name = $column['name'];

    $expr = match ($column['type']) {
        'string' => "\$table->string('{$name}')",
        'text' => "\$table->text('{$name}')",
        'boolean' => "\$table->boolean('{$name}')",
        'integer' => "\$table->integer('{$name}')",
        'unsignedBigInteger' => "\$table->unsignedBigInteger('{$name}')",
        'decimal' => "\$table->decimal('{$name}', {$column['precision']}, {$column['scale']})",
        'date' => "\$table->date('{$name}')",
        'datetime' => "\$table->dateTime('{$name}')",
        'json' => "\$table->json('{$name}')",
        'foreignId' => "\$table->foreignId('{$name}')",
        default => "\$table->string('{$name}')",
    };

    if (! empty($column['nullable'])) {
        $expr .= '->nullable()';
    }

    if (! empty($column['has_default'])) {
        $expr .= '->default('.exportDefault($column['default']).')';
    }

    if (! empty($column['unique'])) {
        $expr .= '->unique()';
    } elseif (! empty($column['index']) || $name === 'status') {
        $expr .= '->index()';
    }

    if ($column['type'] === 'foreignId') {
        $expr .= "->constrained('{$column['table']}')";
        $expr .= ! empty($column['nullable']) ? '->nullOnDelete()' : '->restrictOnDelete()';
    }

    return "            {$expr};";
}

function exportDefault(mixed $value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_int($value) || is_float($value)) {
        return (string) $value;
    }

    if (is_numeric($value) && ! str_starts_with((string) $value, '0')) {
        return (string) $value;
    }

    return "'".str_replace("'", "\\'", (string) $value)."'";
}

function writeModel(string $root, array $entity): void
{
    $traits = [];
    $imports = [
        'Illuminate\\Database\\Eloquent\\Model',
    ];

    $needsBelongs = false;
    $needsHas = false;
    $needsMorph = false;

    foreach ($entity['relations'] as $relation) {
        $needsBelongs = $needsBelongs || $relation['type'] === 'belongsTo';
        $needsHas = $needsHas || $relation['type'] === 'hasMany';
        $needsMorph = $needsMorph || $relation['type'] === 'morphTo';
    }

    if (collect($entity['columns'])->contains(fn ($column) => $column['name'] === 'parent_id')) {
        $needsBelongs = true;
    }

    if ($needsBelongs) {
        $imports[] = 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo';
    }
    if ($needsHas) {
        $imports[] = 'Illuminate\\Database\\Eloquent\\Relations\\HasMany';
    }
    if ($needsMorph) {
        $imports[] = 'Illuminate\\Database\\Eloquent\\Relations\\MorphTo';
    }

    if ($entity['audit']) {
        $traits[] = 'Auditable';
        $imports[] = 'App\\Models\\Concerns\\Auditable';
    }

    if ($entity['buyer']) {
        $traits[] = 'BelongsToBuyerPortal';
        $imports[] = 'App\\Models\\Concerns\\BelongsToBuyerPortal';
    }

    $attribute = '';

    if ($entity['slug']) {
        $traits[] = 'HasUniversalSlug';
        $imports[] = 'ImranDev\\UniversalSlug\\Attributes\\Slug';
        $imports[] = 'ImranDev\\UniversalSlug\\Traits\\HasUniversalSlug';
        $route = $entity['route_key'] ? 'true' : 'false';
        $attribute = "#[Slug(from: '{$entity['slug']}', to: 'slug', skipIfFilled: true, routeKey: {$route})]\n";
    }

    $fillable = array_map(fn ($column) => "        '{$column['name']}',", $entity['columns']);
    $casts = [];

    foreach ($entity['columns'] as $column) {
        $cast = match ($column['type']) {
            'boolean' => 'boolean',
            'decimal' => 'decimal:'.$column['scale'],
            'date' => 'date',
            'datetime' => 'datetime',
            'json' => 'array',
            default => null,
        };

        if ($cast) {
            $casts[] = "            '{$column['name']}' => '{$cast}',";
        }
    }

    $methods = '';

    foreach ($entity['relations'] as $relation) {
        $model = $relation['model'] ?? null;
        $methods .= match ($relation['type']) {
            'belongsTo' => <<<PHP

    public function {$relation['name']}(): BelongsTo
    {
        return \$this->belongsTo({$model}::class, '{$relation['foreign']}');
    }

PHP,
            'hasMany' => $relation['foreign']
                ? <<<PHP

    public function {$relation['name']}(): HasMany
    {
        return \$this->hasMany({$model}::class, '{$relation['foreign']}');
    }

PHP
                : <<<PHP

    public function {$relation['name']}(): HasMany
    {
        return \$this->hasMany({$model}::class);
    }

PHP,
            'morphTo' => <<<PHP

    public function {$relation['name']}(): MorphTo
    {
        return \$this->morphTo();
    }

PHP,
            default => '',
        };
    }

    if (collect($entity['columns'])->contains(fn ($column) => $column['name'] === 'parent_id')) {
        $methods .= <<<PHP

    public function parent(): BelongsTo
    {
        return \$this->belongsTo(self::class, 'parent_id');
    }

PHP;
    }

    $traitLine = $traits === [] ? '' : "\n    use ".implode(', ', $traits).";\n";
    $importLines = implode("\n", array_map(fn ($import) => "use {$import};", $imports));
    $fill = implode("\n", $fillable);
    $cast = implode("\n", $casts);
    $class = $entity['model'];

    $php = <<<PHP
<?php

namespace App\\Models;

{$importLines}

{$attribute}class {$class} extends Model
{{$traitLine}
    protected \$fillable = [
{$fill}
    ];

    protected function casts(): array
    {
        return [
{$cast}
        ];
    }
{$methods}}

PHP;

    file_put_contents($root.'/app/Models/'.$class.'.php', $php);
}

function writePolicy(string $root, array $entity): void
{
    $class = $entity['model'].'Policy';
    $locked = $entity['lock'] ? 'true' : 'false';
    $php = <<<PHP
<?php

namespace App\\Policies;

class {$class} extends ModulePolicy
{
    protected string \$module = '{$entity['module']}';

    protected string \$permission = '{$entity['table']}';

    protected bool \$locked = {$locked};
}

PHP;

    file_put_contents($root.'/app/Policies/'.$class.'.php', $php);
}

function writeResource(string $root, array $entity): void
{
    $model = $entity['model'];
    $resource = $model.'Resource';
    $dir = $root.'/app/Filament/Resources/'.$resource;
    if (! is_dir($dir.'/Pages')) {
        mkdir($dir.'/Pages', 0777, true);
    }

    $php = <<<PHP
<?php

namespace App\\Filament\\Resources;

use App\\Filament\\Resources\\{$resource}\\Pages\\Create{$model};
use App\\Filament\\Resources\\{$resource}\\Pages\\Edit{$model};
use App\\Filament\\Resources\\{$resource}\\Pages\\List{$model}s;
use App\\Models\\{$model};

class {$resource} extends DenimResource
{
    protected static ?string \$model = {$model}::class;

    protected static ?string \$recordTitleAttribute = '{$entity['label']}';

    protected static ?int \$navigationSort = {$entity['sort']};

    protected static string \$iconName = '{$entity['icon']}';

    protected static string \$groupName = '{$entity['group']}';

    public static function getPages(): array
    {
        return [
            'index' => List{$model}s::route('/'),
            'create' => Create{$model}::route('/create'),
            'edit' => Edit{$model}::route('/{record}/edit'),
        ];
    }
}

PHP;

    file_put_contents($root.'/app/Filament/Resources/'.$resource.'.php', $php);

    $list = <<<PHP
<?php

namespace App\\Filament\\Resources\\{$resource}\\Pages;

use App\\Filament\\Resources\\{$resource};
use Filament\\Resources\\Pages\\ListRecords;

class List{$model}s extends ListRecords
{
    protected static string \$resource = {$resource}::class;
}

PHP;

    $create = <<<PHP
<?php

namespace App\\Filament\\Resources\\{$resource}\\Pages;

use App\\Filament\\Resources\\{$resource};
use Filament\\Resources\\Pages\\CreateRecord;

class Create{$model} extends CreateRecord
{
    protected static string \$resource = {$resource}::class;
}

PHP;

    $edit = <<<PHP
<?php

namespace App\\Filament\\Resources\\{$resource}\\Pages;

use App\\Filament\\Resources\\{$resource};
use Filament\\Resources\\Pages\\EditRecord;

class Edit{$model} extends EditRecord
{
    protected static string \$resource = {$resource}::class;
}

PHP;

    file_put_contents($dir.'/Pages/List'.$model.'s.php', $list);
    file_put_contents($dir.'/Pages/Create'.$model.'.php', $create);
    file_put_contents($dir.'/Pages/Edit'.$model.'.php', $edit);
}
