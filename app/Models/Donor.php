<?php

namespace App\Models;

use App\Traits\NearbyScope;
use App\Traits\Uploadable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Donor extends Model
{
    use NearbyScope, HasFactory, Uploadable;
    
    protected $fillable = [
        'id',
        'user_id',
        'contact_number',
        'blood_type',
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

        'verification_status',
        'verification_photo_id',
        'admin_message'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function verificationPhoto(): MorphOne {
        return $this->morphOne(Upload::class, 'uploadable');
    }
}
