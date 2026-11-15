<?php

namespace App\Services;

use App\Models\DyeBatch;
use App\Models\DyeParameter;
use App\Models\ShadeCard;
use App\Models\ShadeReading;

class DyeingIntelligence
{
    public function __construct(private IssueService $issues) {}

    public function reviewParameter(DyeParameter $parameter): void
    {
        $batch = $parameter->batch()->with('shadeCard')->first();

        if (! $batch) {
            return;
        }

        $windows = $this->windows($batch, $batch->shadeCard);
        $faults = [];

        if ($parameter->ph !== null && ($parameter->ph < $windows['ph_min'] || $parameter->ph > $windows['ph_max'])) {
            $faults[] = "pH {$parameter->ph} is outside {$windows['ph_min']}–{$windows['ph_max']} at {$parameter->station}.";
        }

        if ($parameter->orp_mv !== null && $windows['orp_min'] !== null && ($parameter->orp_mv < $windows['orp_min'] || $parameter->orp_mv > $windows['orp_max'])) {
            $faults[] = "ORP {$parameter->orp_mv} mV is outside {$windows['orp_min']} to {$windows['orp_max']} mV. Reduction is weak or over-reduced.";
        }

        if ($parameter->temperature_c !== null && ($parameter->temperature_c < $windows['temp_min'] || $parameter->temperature_c > $windows['temp_max'])) {
            $faults[] = "Temperature {$parameter->temperature_c}°C is outside the box window.";
        }

        $target = $batch->target_concentration_gl ?: $batch->shadeCard?->concentration_gl;

        if ($parameter->concentration_gl !== null && $target) {
            $drift = abs($parameter->concentration_gl - $target) / $target;

            if ($drift > 0.08) {
                $faults[] = 'Concentration has drifted '.round($drift * 100, 1).'% from the set point '.$target.' g/L.';
                $this->issues->open($batch, 'shade-head-tail', 'warning', end($faults) ?: 'Concentration drift', [
                    'station' => $parameter->station,
                ]);
            }
        }

        $previous = DyeParameter::query()
            ->where('dye_batch_id', $batch->id)
            ->where('id', '<', $parameter->id)
            ->latest('id')
            ->first();

        if ($previous?->speed_mpm && $parameter->speed_mpm && abs($parameter->speed_mpm - $previous->speed_mpm) / $previous->speed_mpm > 0.15) {
            $faults[] = "Range speed moved from {$previous->speed_mpm} to {$parameter->speed_mpm} m/min.";
            $this->issues->open($batch, 'streaky-dyeing', 'warning', end($faults), ['station' => $parameter->station]);
        }

        if ($faults !== [] && ($parameter->ph !== null && ($parameter->ph < $windows['ph_min'] || $parameter->ph > $windows['ph_max']))) {
            $this->issues->open($batch, 'streaky-dyeing', 'critical', implode(' ', $faults), [
                'station' => $parameter->station,
                'ph' => $parameter->ph,
            ]);
        } elseif ($faults !== [] && $parameter->orp_mv !== null && $windows['orp_min'] !== null && ($parameter->orp_mv < $windows['orp_min'] || $parameter->orp_mv > $windows['orp_max'])) {
            $this->issues->open($batch, 'dye-spots', 'warning', implode(' ', $faults), ['orp' => $parameter->orp_mv]);
        }
    }

    public function scoreReading(ShadeReading $reading): void
    {
        if ($reading->l_star === null) {
            return;
        }

        $card = $reading->batch()->first()?->shadeCard;

        if (! $card || $card->standard_l === null) {
            return;
        }

        $delta = $this->deltaE(
            (float) $reading->l_star,
            (float) $reading->a_star,
            (float) $reading->b_star,
            (float) $card->standard_l,
            (float) $card->standard_a,
            (float) $card->standard_b,
        );

        if (round((float) $reading->delta_e, 3) === round($delta, 3)) {
            return;
        }

        $reading->forceFill(['delta_e' => $delta])->saveQuietly();
    }

    public function reviewShade(DyeBatch $batch): void
    {
        $readings = $batch->readings()->get()->keyBy('position');
        $headTail = (float) config('denimforge.quality.delta_e_head_tail', 1);
        $side = (float) config('denimforge.quality.delta_e_side_center', 0.8);

        if ($readings->has('head') && $readings->has('tail')) {
            $delta = $this->between($readings['head'], $readings['tail']);

            if ($delta > $headTail) {
                $this->issues->open($batch, 'shade-head-tail', 'critical', 'Head-to-tail delta E is '.round($delta, 2).'. Quarantine the tail.', [
                    'delta_e' => round($delta, 2),
                ]);
            }
        }

        if ($readings->has('center') && ($readings->has('left') || $readings->has('right'))) {
            $worst = 0.0;

            foreach (['left', 'right'] as $edge) {
                if ($readings->has($edge)) {
                    $worst = max($worst, $this->between($readings['center'], $readings[$edge]));
                }
            }

            if ($worst > $side) {
                $this->issues->open($batch, 'shade-side-center', 'warning', 'Side-to-center delta E is '.round($worst, 2).'. Check the nip and box level.', [
                    'delta_e' => round($worst, 2),
                ]);
            }
        }
    }

    /**
     * @return array{ph_min: float, ph_max: float, orp_min: ?float, orp_max: ?float, temp_min: float, temp_max: float}
     */
    public function windows(DyeBatch $batch, ?ShadeCard $card): array
    {
        $sulfur = $batch->method === 'sulfur';

        return [
            'ph_min' => (float) ($card->ph_min ?? ($sulfur ? 9.0 : 11.2)),
            'ph_max' => (float) ($card->ph_max ?? ($sulfur ? 11.5 : 11.8)),
            'orp_min' => $sulfur ? null : (float) ($card->orp_min ?? -860),
            'orp_max' => $sulfur ? null : (float) ($card->orp_max ?? -740),
            'temp_min' => (float) ($card->temp_min ?? ($sulfur ? 55 : 18)),
            'temp_max' => (float) ($card->temp_max ?? ($sulfur ? 95 : 35)),
        ];
    }

    public function deltaE(float $l1, float $a1, float $b1, float $l2, float $a2, float $b2): float
    {
        return round(sqrt(($l1 - $l2) ** 2 + ($a1 - $a2) ** 2 + ($b1 - $b2) ** 2), 3);
    }

    public function between(ShadeReading $left, ShadeReading $right): float
    {
        return $this->deltaE(
            (float) $left->l_star,
            (float) $left->a_star,
            (float) $left->b_star,
            (float) $right->l_star,
            (float) $right->a_star,
            (float) $right->b_star,
        );
    }
}
