<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait NearbyScope
{
    /**
     * Scope to find nearby records within a given radius (in kilometers).
     */

    public function scopeNearby(Builder $query, $latitude = 27.7172, $longitude = 85.3240, $radiusInKm = 10): Builder
    {
        if (!$latitude || !$longitude) {
            return $query;
        }

        $latitude = (float) $latitude;
        $longitude = (float) $longitude;
        $radiusInKm = (float) $radiusInKm;

        $isPostgreSQL = config('database.default') === 'pgsql';

        $distanceFormula = "(6371 * acos(
        cos(radians(?)) *
        cos(radians(latitude)) *
        cos(radians(longitude) - radians(?)) +
        sin(radians(?)) *
        sin(radians(latitude))
    ))";

        $query = $query
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw("*, {$distanceFormula} AS distance", [$latitude, $longitude, $latitude]);

        if ($isPostgreSQL) {
            // PostgreSQL: use WHERE for filtering
            return $query
                ->whereRaw("{$distanceFormula} <= ?", [$latitude, $longitude, $latitude, $radiusInKm])
                ->orderByRaw("{$distanceFormula} ASC", [$latitude, $longitude, $latitude]);
        } else {
            // MySQL: use HAVING as before
            return $query
                ->having("distance", "<=", $radiusInKm)
                ->orderBy("distance");
        }
    }
}
