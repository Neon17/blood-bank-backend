<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class DonationProgram extends Model
{
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'location',
        'organizer',
        'status',
        'created_by',
    ];

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bloodBank() {
        return $this->belongsTo(BloodBank::class);
    }

    public function visibility(): MorphOne
    {
        return $this->morphOne(Visibility::class, 'visible');
    }
}
