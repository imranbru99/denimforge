<?php

namespace App\Services;

use App\Domain\DenimKnowledge;
use App\Models\InspectionDefect;

class DefectAnalyzer
{
    public function annotate(InspectionDefect $defect): void
    {
        $haystack = strtolower($defect->defect_code.' '.$defect->notes);
        $code = match (true) {
            str_contains($haystack, 'streak') => 'streaky-dyeing',
            str_contains($haystack, 'spot') || str_contains($haystack, 'stain') => 'dye-spots',
            str_contains($haystack, 'skew') || str_contains($haystack, 'shrink') => 'uneven-shrinkage',
            str_contains($haystack, 'double') => 'double-pick',
            str_contains($haystack, 'reed') => 'reed-marks',
            str_contains($haystack, 'slub') => 'slubs',
            str_contains($haystack, 'wash') || str_contains($haystack, 'abrasion') => 'poor-washdown',
            $defect->classification === 'dyeing' => 'dye-spots',
            $defect->classification === 'finishing' => 'uneven-shrinkage',
            $defect->classification === 'washing' => 'poor-washdown',
            $defect->classification === 'yarn' => 'slubs',
            default => 'broken-end',
        };

        $library = DenimKnowledge::find($code);
        $summary = [
            'engine' => 'mill-rules',
            'defect_code' => $code,
            'summary' => $library['symptoms'] ?? $defect->notes,
            'action' => $library['remedies'] ?? 'Hold the piece and call the department supervisor.',
        ];

        if ($defect->ai_detection === $summary) {
            return;
        }

        $defect->forceFill(['ai_detection' => $summary])->saveQuietly();
    }
}
