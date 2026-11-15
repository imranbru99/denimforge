<?php

namespace App\Models\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToBuyerPortal
{
    public static function bootBelongsToBuyerPortal(): void
    {
        static::addGlobalScope('buyer-portal', function (Builder $query): void {
            $user = auth()->user();

            if (! $user instanceof User || ! $user->buyer_id || ! $user->hasRole('buyer')) {
                return;
            }

            $table = $query->getModel()->getTable();

            if (in_array('buyer_id', $query->getModel()->getFillable(), true)) {
                $query->where($table.'.buyer_id', $user->buyer_id);
            }
        });
    }
}
