<?php

namespace App\Models;

use App\Models\Scopes\LastDonatedScope;
use App\Traits\NearbyScope;
use App\Traits\Uploadable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

#[ScopedBy(LastDonatedScope::class)]
class Donor extends Model
{
    use NearbyScope, HasFactory, Uploadable;

    protected $appends = ['distance_in_km'];

    protected $fillable = [
        'id',
        'user_id',
        'contact_number',
        'blood_group',
        'address',
        'date_of_birth',

        'weight',
        'height',
        'last_donated_date',
        'medical_conditions',
        'current_medication',
        'current_health_status',

        'latitude',
        'longitude',

        'blood_pressure_systolic',
        'blood_pressure_diastolic',

        // 'eligible_to_donate',

        'verification_status',
        'verification_photo_id',
        'admin_message'
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(('active'), function (Builder $builder) {
            $builder->whereNotNull('last_donated_date');
            $builder->where('last_donated_date', '<=', now()->subDays(90)->toDateString());
            $builder->where('eligible_to_donate', true);
        });
    }

    protected function distanceInKm(): Attribute
    {
        return Attribute::make(
            get: fn() =>
            round(haversineGreatCircleDistance($this->latitude, $this->longitude), 2)
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verificationPhoto(): MorphOne
    {
        return $this->morphOne(Upload::class, 'uploadable');
    }
}
