<?php

namespace App\Services;

use App\Domain\Schema;
use App\Models\LotLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TraceabilityService
{
    public function link(Model $from, Model $to, ?float $qty = null, ?string $uom = null, ?string $note = null): LotLink
    {
        return LotLink::query()->create([
            'origin_type' => $from::class,
            'origin_id' => $from->getKey(),
            'destination_type' => $to::class,
            'destination_id' => $to->getKey(),
            'qty' => $qty,
            'uom' => $uom,
            'note' => $note,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function lineage(Model $model): array
    {
        return [
            'record' => $this->describe($model),
            'upstream' => $this->walk($model, 'destination', 'origin'),
            'downstream' => $this->walk($model, 'origin', 'destination'),
        ];
    }

    public function findByCode(string $code): ?Model
    {
        foreach (Schema::traceableModels() as $class) {
            $key = $this->businessKey($class);
            $found = $class::query()
                ->where(function ($query) use ($code, $key) {
                    $query->where('trace_code', $code);

                    if ($key !== 'id') {
                        $query->orWhere($key, $code);
                    }
                })
                ->first();

            if ($found) {
                return $found;
            }
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function walk(Model $start, string $match, string $follow, int $depth = 6): array
    {
        $seen = [];
        $queue = [[$start, 0]];
        $found = [];

        while ($queue !== []) {
            [$node, $level] = array_shift($queue);

            if ($level >= $depth) {
                continue;
            }

            $links = LotLink::query()
                ->where($match.'_type', $node::class)
                ->where($match.'_id', $node->getKey())
                ->get();

            foreach ($links as $link) {
                $next = $link->{$follow}()->first();

                if (! $next) {
                    continue;
                }

                $key = $next::class.'#'.$next->getKey();

                if (isset($seen[$key])) {
                    continue;
                }

                $seen[$key] = true;
                $found[] = $this->describe($next) + ['via_qty' => $link->qty, 'uom' => $link->uom, 'note' => $link->note];
                $queue[] = [$next, $level + 1];
            }
        }

        return $found;
    }

    /**
     * @return array<string, mixed>
     */
    public function describe(Model $model): array
    {
        return [
            'type' => class_basename($model),
            'id' => $model->getKey(),
            'label' => $model->trace_code
                ?? $model->batch_no
                ?? $model->lot_no
                ?? $model->roll_no
                ?? $model->code
                ?? $model->number
                ?? $model->name
                ?? ('#'.$model->getKey()),
            'trace_code' => $model->trace_code ?? null,
        ];
    }

    private function businessKey(string $class): string
    {
        $model = new $class;

        foreach (['batch_no', 'lot_no', 'roll_no', 'code', 'number', 'marker_no', 'inspection_no'] as $key) {
            if (in_array($key, $model->getFillable(), true)) {
                return $key;
            }
        }

        return 'id';
    }
}
