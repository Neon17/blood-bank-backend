<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    //
    protected $table = 'blood_requests';

    protected $fillable = [
        'blood_type',
        'quantity',
        'date_time',
        'location',
        'contact_number',
        'user_id',
        'blood_bank_id',
        'status',
        'donated_by',
        'donated_by_user',
        'donated_by_blood_banks',
        'verified_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bloodBank()
    {
        return $this->belongsTo(BloodBank::class);
    }
}
