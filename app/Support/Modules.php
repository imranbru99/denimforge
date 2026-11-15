<?php

namespace App\Support;

use App\Models\ModuleToggle;
use Illuminate\Support\Facades\Schema;

class Modules
{
    public static function enabled(string $key): bool
    {
        if ($key === 'core') {
            return true;
        }

        $map = self::map();

        if (! array_key_exists($key, $map)) {
            return array_key_exists($key, config('denimforge.modules', []));
        }

        return (bool) $map[$key];
    }

    /**
     * @return array<string, bool>
     */
    public static function map(): array
    {
        if (app()->bound('denimforge.module-map')) {
            return app('denimforge.module-map');
        }

        $map = [];

        try {
            if (Schema::hasTable('module_toggles')) {
                $map = ModuleToggle::query()->pluck('enabled', 'key')->map(fn ($value) => (bool) $value)->all();
            }
        } catch (\Throwable) {
            $map = [];
        }

        app()->instance('denimforge.module-map', $map);

        return $map;
    }

    public static function forget(): void
    {
        app()->forgetInstance('denimforge.module-map');
    }
}
