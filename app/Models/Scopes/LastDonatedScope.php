<?php

namespace App\Models\Scopes;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class LastDonatedScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     * This scope filters donors who have not donated in the last 90 days.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $now = Carbon::now();
        $builder->whereNotNull('last_donated_date')
                ->where('last_donated_date', '<', $now->subDays(90)->toDateString());
    }
}
