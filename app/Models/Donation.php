<?php

namespace App\Models;

use App\Traits\Uploadable;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use Uploadable;
    protected $table = 'donations';

    protected $fillable = [
        'quantity',
        'blood_type',
        'date_time',
        'exact_location',
        'contact_number',
        'contact_name',
        'contact_email',
        'city',
        'state',
        'country',
        'latitude',
        'longitude',
        'blood_request_id',
        'user_id',
        'blood_bank_id',
        'donation_program_id',
        'status'
    ];

    public function bloodRequest()
    {
        return $this->belongsTo(BloodRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bloodBank()
    {
        return $this->belongsTo(BloodBank::class);
    }

    public function donationProgram()
    {
        return $this->belongsTo(DonationProgram::class);
    }
}
