<?php

namespace App\Services;

use App\Domain\DenimKnowledge;
use App\Models\DyeBatch;
use App\Models\ShadeReading;
use ImranDevBd\AiHub\Facades\AIHub;
use Throwable;

class ShadeAnalyzer
{
    public function __construct(private DyeingIntelligence $dyeing, private IssueService $issues) {}

    /**
     * @return array<string, mixed>
     */
    public function analyze(DyeBatch $batch, ?string $note = null): array
    {
        $batch->load('readings', 'shadeCard', 'parameters');
        $heuristic = $this->heuristic($batch, $note);

        if (! config('denimforge.ai_ready')) {
            return $heuristic + ['engine' => 'mill-rules'];
        }

        try {
            $response = AIHub::system('You are a denim dyeing master. Reply with JSON only.')
                ->prompt($this->prompt($batch, $note))
                ->structured([
                    'type' => 'object',
                    'properties' => [
                        'defect_code' => ['type' => 'string'],
                        'delta_e' => ['type' => 'number'],
                        'summary' => ['type' => 'string'],
                        'action' => ['type' => 'string'],
                    ],
                    'required' => ['defect_code', 'summary', 'action'],
                ])
                ->send();

            $json = $response->json() ?: [];
            $code = $json['defect_code'] ?? $heuristic['defect_code'];
            $library = DenimKnowledge::find($code) ?? DenimKnowledge::find($heuristic['defect_code']);

            $result = [
                'engine' => 'ai-hub',
                'provider' => $response->provider,
                'model' => $response->model,
                'defect_code' => $code,
                'delta_e' => $json['delta_e'] ?? $heuristic['delta_e'],
                'summary' => $json['summary'] ?? $heuristic['summary'],
                'action' => $json['action'] ?? ($library['remedies'] ?? $heuristic['action']),
            ];
        } catch (Throwable $exception) {
            $result = $heuristic + ['engine' => 'mill-rules', 'ai_error' => $exception->getMessage()];
        }

        if (($result['delta_e'] ?? 0) > 1 || ($result['defect_code'] ?? '') !== 'within-tolerance') {
            $this->issues->open($batch, $result['defect_code'], 'warning', $result['summary']);
        }

        $reading = $batch->readings->firstWhere('photo_path', '!=', null) ?? $batch->readings->last();

        if ($reading) {
            $reading->forceFill(['ai_summary' => $result])->saveQuietly();
        }

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    private function heuristic(DyeBatch $batch, ?string $note): array
    {
        $this->dyeing->reviewShade($batch);
        $readings = $batch->readings;
        $delta = null;

        if ($readings->firstWhere('position', 'head') && $readings->firstWhere('position', 'tail')) {
            $delta = $this->dyeing->between(
                $readings->firstWhere('position', 'head'),
                $readings->firstWhere('position', 'tail'),
            );
        }

        $code = 'within-tolerance';
        $summary = 'Shade is inside the mill window.';
        $action = 'Release the set to weaving.';

        if ($delta !== null && $delta > (float) config('denimforge.quality.delta_e_head_tail', 1)) {
            $code = 'shade-head-tail';
        } elseif ($note && str_contains(strtolower($note), 'streak')) {
            $code = 'streaky-dyeing';
        } elseif ($note && str_contains(strtolower($note), 'spot')) {
            $code = 'dye-spots';
        }

        $library = DenimKnowledge::find($code);

        if ($library) {
            $summary = $library['symptoms'];
            $action = $library['remedies'];
        }

        return [
            'defect_code' => $code,
            'delta_e' => $delta,
            'summary' => $summary,
            'action' => $action,
        ];
    }

    private function prompt(DyeBatch $batch, ?string $note): string
    {
        $lines = $batch->readings->map(fn ($reading) => sprintf(
            '%s L*%s a*%s b*%s dE %s',
            $reading->position,
            $reading->l_star,
            $reading->a_star,
            $reading->b_star,
            $reading->delta_e,
        ))->implode("\n");

        $codes = collect(DenimKnowledge::articles())->pluck('code')->implode(', ');

        return "Batch {$batch->batch_no} method {$batch->method}. Note: {$note}\nReadings:\n{$lines}\nChoose defect_code from: {$codes}, within-tolerance.";
    }
}
