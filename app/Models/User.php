<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\NearbyScope;
use App\Traits\Uploadable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, NearbyScope, Uploadable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'address',
        'role',
        'dob',
        'phone_number',
        'password',
        'city',
        'country',
        // latitude and longitude are for current location
        'current_city',
        'latitude',
        'longitude',
        'blood_group',
        'will_donate',
        'verified_as_donor',
        'last_donated',
        'profile_photo_id'
    ];

    protected function scopeDonors($query) {
        return $query->where('will_donate', true);
    }

    public function bloodRequests() {
        return $this->hasMany(BloodRequest::class);
    }

    public function donorApplication() {
        return $this->hasOne(Donor::class);
    }

    public function isAdmin() {
        return $this->role === 'admin';
    }

    public function profilePhoto(): MorphOne {
        return $this->morphOne(Upload::class, 'uploadable');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'dob' => 'datetime',
            'last_donated' => 'datetime',
            'will_donate' => 'boolean',
            'verified_as_donor' => 'boolean',
            'latitude' => 'float',
            'longitude' => 'float',
            'role' => 'string',
            'last_verified' => 'datetime',
            'last_donated' => 'datetime',
            'last_verified' => 'datetime',
        ];
    }
}
