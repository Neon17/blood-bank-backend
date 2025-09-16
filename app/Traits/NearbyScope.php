<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait NearbyScope
{
    /**
     * Scope to find nearby records within a given radius (in kilometers).
     */

    public function scopeNearby(Builder $query, $latitude, $longitude, $radiusInKm = 10): Builder
    {
        if (!$latitude || !$longitude) {
            return $query;
        }

        $latitude = (float) $latitude;
        $longitude = (float) $longitude;
        $radiusInKm = (float) $radiusInKm;

        return $query
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw(
                "*, (6371 * acos(
                cos(radians(?)) *
                cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) *
                sin(radians(latitude))
            )) AS distance",
                [$latitude, $longitude, $latitude]
            )
            ->having("distance", "<=", $radiusInKm)
            ->orderBy("distance");
    }
}
