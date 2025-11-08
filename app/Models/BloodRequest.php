<?php

namespace App\Models;

use App\Traits\NearbyScope;
use App\Traits\Uploadable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BloodRequest extends Model
{
    //
    use NearbyScope, HasFactory, Uploadable;

    protected $table = 'blood_requests';
    protected $appends = ['distance_in_km'];

    protected $fillable = [
        'blood_type',
        'quantity',
        'date_time',
        'exact_location',
        'latitude',
        'longitude',
        'contact_number',
        'date_time',
        'city',
        'state',
        'country',
        'user_id',
        'blood_bank_id',
        'status',
        'active_status', //active_status means whether the request is active or is fulfilled
        'status',
        'verification_status',
        'donated_by',
        'donated_by_user',
        'donated_by_blood_banks',
        'verified_by',
    ];

    protected static function booted(){
        static::addGlobalScope('active', function(Builder $builder){
            $builder->where('active_status', true);
            $builder->where('verification_status', 'approved');
        });
    }

    public function setLatitudeAttribute($value)
    {
        $this->attributes['latitude'] = round($value, 8);
    }

    public function setLongitudeAttribute($value)
    {
        $this->attributes['longitude'] = round($value, 8);
    }

    protected function distanceInKm(): Attribute
    {
        return Attribute::make(
            get: fn () =>
                round(haversineGreatCircleDistance($this->latitude, $this->longitude), 2)
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bloodBank()
    {
        return $this->belongsTo(BloodBank::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function verificationPhoto()
    {
        return $this->morphOne(Upload::class, 'uploadable');
    }

    public function visibility()
    {
        return $this->morphOne(Visibility::class, 'visible');
    }
}
