<?php

namespace App\Services;

use App\Models\Inspection;
use App\Models\InspectionDefect;

class FourPoint
{
    public static function points(float $inches): int
    {
        if ($inches <= 0) {
            return 0;
        }

        if ($inches <= 3) {
            return 1;
        }

        if ($inches <= 6) {
            return 2;
        }

        if ($inches <= 9) {
            return 3;
        }

        return 4;
    }

    public function recalculate(Inspection $inspection): Inspection
    {
        $points = (int) $inspection->defects()->sum('points');
        $yards = (float) $inspection->yards;
        $width = (float) ($inspection->width_inch ?: 0);
        $squareYards = $width > 0 ? $yards * ($width / 36) : 0;
        $perHundred = $squareYards > 0 ? round($points * 100 / $squareYards, 2) : 0;
        $limit = (float) ($inspection->accept_limit ?: config('denimforge.quality.four_point_limit', 28));
        $result = $yards <= 0 ? 'pending' : ($perHundred <= $limit ? 'pass' : 'fail');

        $inspection->forceFill([
            'total_points' => $points,
            'points_per_100' => $perHundred,
            'result' => $result,
        ])->saveQuietly();

        if ($result === 'fail') {
            $classification = $inspection->defects()->value('classification');
            $code = match ($classification) {
                'dyeing' => 'dye-spots',
                'finishing' => 'uneven-shrinkage',
                'washing' => 'poor-washdown',
                'yarn' => 'slubs',
                default => 'reed-marks',
            };

            app(IssueService::class)->open(
                $inspection,
                $code,
                'warning',
                "4-point score {$perHundred} exceeds the buyer limit of {$limit} points per 100 square yards.",
            );
        }

        return $inspection->refresh();
    }

    public function applyPoints(InspectionDefect $defect): void
    {
        $points = self::points((float) $defect->length_inch);

        if ((int) $defect->points !== $points) {
            $defect->points = $points;
        }
    }
}
