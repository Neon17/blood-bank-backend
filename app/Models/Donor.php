<?php

namespace App\Models;

use App\Traits\NearbyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use NearbyScope, HasFactory;
    
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
        'admin_message'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
