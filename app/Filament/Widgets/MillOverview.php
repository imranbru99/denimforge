<?php

namespace App\Filament\Widgets;

use App\Models\Alert;
use App\Models\DyeBatch;
use App\Models\Inspection;
use App\Models\Material;
use App\Models\ProcessIssue;
use App\Services\AttendanceBridge;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MillOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Live mill';

    protected int|array|null $columns = 3;

    protected function getStats(): array
    {
        $inspections = Inspection::query()->whereIn('result', ['pass', 'fail'])->count();
        $failed = Inspection::query()->where('result', 'fail')->count();
        $rejection = $inspections > 0 ? round($failed / $inspections * 100, 1) : 0;
        $clock = app(AttendanceBridge::class);

        return [
            Stat::make('Running dye sets', DyeBatch::query()->where('status', 'running')->count())
                ->description($clock->plantClock().' plant time')
                ->color('info'),
            Stat::make('Open process issues', ProcessIssue::query()->where('status', '!=', 'resolved')->count())
                ->description('Dyeing, weaving, finishing, wash')
                ->color('warning'),
            Stat::make('Quality rejection', $rejection.'%')
                ->description($failed.' failed of '.$inspections.' decided lots')
                ->color($rejection > 8 ? 'danger' : 'success'),
            Stat::make('Punches today', $clock->todayCount())
                ->description('Attendance hub')
                ->color('gray'),
            Stat::make('Open alerts', Alert::query()->where('status', 'open')->count())
                ->description('Mail, and SMS or WhatsApp when a gateway is switched on')
                ->color('danger'),
            Stat::make('Below reorder', Material::query()->where('is_active', true)->whereColumn('qty_on_hand', '<=', 'reorder_level')->where('reorder_level', '>', 0)->count())
                ->description('FIFO lots still apply')
                ->color('warning'),
        ];
    }
}
