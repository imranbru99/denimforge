<?php

namespace App\Services;

use App\Models\FabricProduct;
use ImranDevBd\AiCatalog\Facades\AiCatalog;
use Throwable;

class CatalogAssistant
{
    /**
     * @return array<string, mixed>
     */
    public function draft(FabricProduct $product): array
    {
        $local = [
            'title' => $product->name,
            'description' => trim($product->name.' is a '.$product->weight_oz.' oz '.$product->construction.' denim, '.$product->composition.', shade '.$product->shade.', finish '.$product->finish.'.'),
            'engine' => 'mill-copy',
        ];

        if (! config('denimforge.ai_ready')) {
            return $local;
        }

        try {
            $generation = AiCatalog::fromKeyword($product->name.' '.$product->composition.' '.$product->shade)->generate();

            return [
                'engine' => 'ai-catalog',
                'generation_id' => $generation->id,
                'title' => data_get($generation->payload, 'title', $local['title']),
                'description' => data_get($generation->payload, 'long_description', data_get($generation->payload, 'short_description', $local['description'])),
            ];
        } catch (Throwable $exception) {
            return $local + ['ai_error' => $exception->getMessage()];
        }
    }
}
