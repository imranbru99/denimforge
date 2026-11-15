<?php

namespace App\Services;

use App\Models\FactoryDocument;
use ImranDevBd\AiOcr\Facades\AiOcr;
use Throwable;

class DocumentIntake
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function store(string $path, array $attributes = []): FactoryDocument
    {
        $document = FactoryDocument::query()->create([
            'title' => $attributes['title'] ?? basename($path),
            'document_type' => $attributes['document_type'] ?? 'other',
            'path' => $path,
            'status' => 'uploaded',
            'source_type' => $attributes['source_type'] ?? null,
            'source_id' => $attributes['source_id'] ?? null,
            'notes' => $attributes['notes'] ?? null,
        ]);

        return $this->extract($document);
    }

    public function extract(FactoryDocument $document): FactoryDocument
    {
        $absolute = str_starts_with($document->path, DIRECTORY_SEPARATOR)
            ? $document->path
            : storage_path('app/public/'.$document->path);

        if (! is_file($absolute)) {
            $absolute = storage_path('app/'.$document->path);
        }

        try {
            $result = AiOcr::file($absolute)->manualOnly()->extract();
            $document->forceFill([
                'status' => 'extracted',
                'extracted' => [
                    'text' => $result->text(),
                    'data' => $result->toArray(),
                ],
            ])->save();
        } catch (Throwable $exception) {
            $document->forceFill([
                'status' => 'failed',
                'extracted' => ['error' => $exception->getMessage()],
            ])->save();
        }

        return $document->refresh();
    }
}
