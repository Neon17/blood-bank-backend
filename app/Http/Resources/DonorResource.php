<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DonorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->resource->load('user');
        $this->resource->user->load('profilePhoto');

        $distance_in_km = null;
        if ($this->latitude && $this->longitude && $request->has(['latitude', 'longitude'])) {
            $distance_in_km = haversineGreatCircleDistance(
                (float) $request->input('latitude'),
                (float) $request->input('longitude'),
                (float) $this->latitude,
                (float) $this->longitude
            );

            $distance_in_km = round($distance_in_km, 2);
        }

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            'contact_number' => $this->contact_number,
            'blood_group' => $this->blood_group,
            'address' => $this->address,
            'date_of_birth' => $this->date_of_birth,

            'weight' => $this->weight,
            'height' => $this->height,
            'last_donated_date' => $this->last_donated_date,
            'medical_conditions' => $this->medical_conditions,
            'current_medication' => $this->current_medication,
            'current_health_status' => $this->current_health_status,

            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'distance_in_km' => $distance_in_km,
            'country' => $this->country,
            'city' => $this->city,

            'verification_status' => $this->verification_status,
            'admin_message' => $this->admin_message,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
