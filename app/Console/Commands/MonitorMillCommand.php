<?php

namespace App\Console\Commands;

use App\Models\DyeBatch;
use App\Models\Machine;
use App\Services\DyeingIntelligence;
use App\Services\IssueService;
use App\Services\StockService;
use Illuminate\Console\Command;

class MonitorMillCommand extends Command
{
    protected $signature = 'denimforge:monitor';

    protected $description = 'Review running dye lots, low stock, and machines that stop too often';

    public function handle(DyeingIntelligence $dyeing, StockService $stock, IssueService $issues): int
    {
        DyeBatch::query()->where('status', 'running')->each(function (DyeBatch $batch) use ($dyeing) {
            $latest = $batch->parameters()->latest('recorded_at')->first();

            if ($latest) {
                $dyeing->reviewParameter($latest);
            }

            $dyeing->reviewShade($batch);
        });

        $low = $stock->flagLowStock();

        $predictive = 0;

        Machine::query()->each(function (Machine $machine) use ($issues, &$predictive) {
            $recent = $machine->stoppages()->where('started_at', '>=', now()->subDays(7))->count();

            if ($recent >= 3) {
                $issues->alert(
                    $machine,
                    null,
                    'warning',
                    'Predictive maintenance: '.$machine->name,
                    $machine->code.' stopped '.$recent.' times in seven days.',
                    'Open a preventive work order, inspect the last three stop reasons, and check bearings, belts, and electrical load.',
                );
                $predictive++;
            }
        });

        $this->info("Low-stock alerts: {$low}. Predictive machine alerts: {$predictive}.");

        return self::SUCCESS;
    }
}
