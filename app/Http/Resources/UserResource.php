<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->getRoleAttribute(),
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'city' => $this->city,
            'country' => $this->country,
            'current_city' => $this->current_city,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'blood_group' => $this->blood_group,
            'will_donate' => $this->will_donate,
            'verified_as_donor' => $this->verified_as_donor,
            'last_donated' => $this->last_donated,
            'profile_photo_id' => $this->profile_photo_id,

            'profilePhoto' => [
                'id' => $this->profilePhoto ? $this->profilePhoto->id : null,
                'name' => $this->profilePhoto ? $this->profilePhoto->name : null,
                'path' => $this->profilePhoto ? $this->profilePhoto->path : null,
                'url' => $this->profilePhoto ? $this->getUploadUrl() : null,
                'storage_in_kb' => $this->profilePhoto ? $this->profilePhoto->storage_in_kb : null,
                'uploadable_id' => $this->profilePhoto->uploadable_id ?? null,
                'uploadable_type' => $this->profilePhoto->uploadable_type ?? null,
                'created_at' => $this->profilePhoto ? $this->profilePhoto->created_at : null,
                'updated_at' => $this->profilePhoto ? $this->profilePhoto->updated_at : null
            ],

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
