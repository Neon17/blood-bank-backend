<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class BloodBank extends Model
{
    use HasFactory;

    public $fillable = [
        'name',
        'address',
        'amount_of_blood',
        'phone_number'
    ];

    public function bloodRequests() {
        return $this->hasMany(BloodRequest::class);
    }

    public function donationPrograms() {
        return $this->hasMany(DonationProgram::class);
    }

    public function user(): MorphOne {
        return $this->morphOne(User::class, 'userable');
    }
}
