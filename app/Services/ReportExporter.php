<?php

namespace App\Services;

use App\Models\DyeBatch;
use App\Models\HourlyOutput;
use App\Models\Inspection;
use App\Models\StyleCosting;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportExporter
{
    public function download(string $report): BinaryFileResponse
    {
        $rows = match ($report) {
            'production' => $this->production(),
            'quality' => $this->quality(),
            'shade' => $this->shade(),
            'cost' => $this->cost(),
            default => abort(404),
        };

        $path = storage_path('app/denimforge-'.$report.'-'.uniqid().'.xlsx');
        $writer = new Writer;
        $writer->openToFile($path);

        foreach ($rows as $row) {
            $writer->addRow(Row::fromValues($row));
        }

        $writer->close();

        return response()->download($path, $report.'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend();
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private function production(): array
    {
        $rows = [['Line', 'Hour', 'Style', 'Target', 'Actual', 'Efficiency %']];

        HourlyOutput::query()->with(['line', 'style'])->latest('hour_start')->limit(500)->get()->each(function ($output) use (&$rows) {
            $rows[] = [
                $output->line?->name,
                optional($output->hour_start)->format('Y-m-d H:i'),
                $output->style?->code,
                $output->target,
                $output->actual,
                $output->efficiency,
            ];
        });

        return $rows;
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private function quality(): array
    {
        $rows = [['Inspection', 'Stage', 'Points', 'Per 100 sq yd', 'Result']];

        Inspection::query()->latest('id')->limit(500)->get()->each(function ($inspection) use (&$rows) {
            $rows[] = [$inspection->inspection_no, $inspection->stage, $inspection->total_points, $inspection->points_per_100, $inspection->result];
        });

        return $rows;
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private function shade(): array
    {
        $rows = [['Batch', 'Method', 'Status', 'Readings']];

        DyeBatch::query()->withCount('readings')->latest('id')->limit(500)->get()->each(function ($batch) use (&$rows) {
            $rows[] = [$batch->batch_no, $batch->method, $batch->status, $batch->readings_count];
        });

        return $rows;
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private function cost(): array
    {
        $rows = [['Style', 'Fabric', 'Dyeing', 'Weaving', 'Washing', 'Labor', 'Trim', 'Overhead', 'Total', 'Quoted', 'Variance']];

        StyleCosting::query()->with('style')->latest('id')->limit(500)->get()->each(function ($cost) use (&$rows) {
            $rows[] = [
                $cost->style?->code,
                $cost->fabric_cost,
                $cost->dyeing_cost,
                $cost->weaving_cost,
                $cost->washing_cost,
                $cost->labor_cost,
                $cost->trim_cost,
                $cost->overhead,
                $cost->total,
                $cost->quoted_price,
                $cost->variance,
            ];
        });

        return $rows;
    }
}
