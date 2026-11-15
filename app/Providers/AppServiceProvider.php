<?php

namespace App\Providers;

use App\Models\Bundle;
use App\Models\DyeParameter;
use App\Models\HourlyOutput;
use App\Models\InspectionDefect;
use App\Models\ModuleToggle;
use App\Models\ShadeReading;
use App\Models\StyleCosting;
use App\Models\User;
use App\Services\DefectAnalyzer;
use App\Services\DyeingIntelligence;
use App\Services\FourPoint;
use App\Support\Modules;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use ImranDevBd\AiCatalog\Models\CatalogGeneration;
use ImranDevBd\AttendanceHub\Models\AttendanceDevice;
use ImranDevBd\AttendanceHub\Models\AttendanceLog;
use ImranDevBD\LaravelAdminPanel\AdminPanel;
use ImranDevBD\LaravelAdminPanel\Navigation\NavigationItem;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        config([
            'admin-panel.prefix' => env('ADMIN_PANEL_PREFIX', 'console'),
            'admin-panel.middleware' => ['web', 'auth'],
            'admin-panel.theme.brand_name' => 'DenimForge Console',
            'admin-panel.theme.default_palette' => 'indigo',
            'admin-panel.auth.enable_registration' => false,
            'admin-panel.auth.enable_2fa' => false,
            'admin-panel.auth.enable_passkeys' => false,
        ]);

        View::prependNamespace('admin-panel', resource_path('views/vendor/admin-panel'));

        Gate::define('viewAiHub', fn (User $user): bool => $user->hasAnyRole(['super-admin', 'factory-manager']));

        Gate::before(function (User $user, string $ability, array $arguments): ?bool {
            $target = $arguments[0] ?? null;
            $class = is_object($target) ? $target::class : (is_string($target) ? $target : null);

            if (in_array($class, [AttendanceDevice::class, AttendanceLog::class], true)) {
                return $user->hasAnyRole(['super-admin', 'factory-manager', 'hr']) ? null : false;
            }

            if ($class === CatalogGeneration::class) {
                return $user->hasAnyRole(['super-admin', 'factory-manager']) ? null : false;
            }

            return null;
        });

        DyeParameter::saved(function (DyeParameter $parameter): void {
            app(DyeingIntelligence::class)->reviewParameter($parameter);
        });

        ShadeReading::saved(function (ShadeReading $reading): void {
            $dyeing = app(DyeingIntelligence::class);
            $dyeing->scoreReading($reading->refresh());

            if ($reading->batch) {
                $dyeing->reviewShade($reading->batch->load('readings'));
            }
        });

        InspectionDefect::saving(function (InspectionDefect $defect): void {
            app(FourPoint::class)->applyPoints($defect);
        });

        InspectionDefect::saved(function (InspectionDefect $defect): void {
            if ($defect->inspection) {
                app(FourPoint::class)->recalculate($defect->inspection);
            }

            app(DefectAnalyzer::class)->annotate($defect);
        });

        ModuleToggle::saved(fn () => Modules::forget());
        ModuleToggle::deleted(fn () => Modules::forget());

        Bundle::creating(function (Bundle $bundle): void {
            if (! $bundle->qr_payload && $bundle->code) {
                $bundle->qr_payload = 'DF|'.$bundle->code.'|'.$bundle->trace_code;
            }
        });

        HourlyOutput::saving(function (HourlyOutput $output): void {
            $target = (int) $output->target;
            $output->efficiency = $target > 0 ? round(((int) $output->actual) / $target * 100, 2) : 0;
        });

        StyleCosting::saving(function (StyleCosting $costing): void {
            $total = (float) $costing->fabric_cost
                + (float) $costing->dyeing_cost
                + (float) $costing->weaving_cost
                + (float) $costing->washing_cost
                + (float) $costing->labor_cost
                + (float) $costing->trim_cost
                + (float) $costing->overhead;
            $costing->total = round($total, 4);
            $costing->variance = $costing->quoted_price === null
                ? null
                : round((float) $costing->quoted_price - $total, 4);
        });

        $panel = app(AdminPanel::class)->getPanel('admin');
        $panel->navigation([
            NavigationItem::make('Mill ERP')->url('/admin')->icon('factory'),
            NavigationItem::make('Public website')->url('/')->icon('globe'),
        ]);
    }
}
