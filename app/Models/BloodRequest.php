<?php

namespace App\Models;

use App\Traits\NearbyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class BloodRequest extends Model
{
    //
    use NearbyScope;

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
        'active_status', //active_status means whether the request is active or is fulfilled
        'donated_by',
        'donated_by_user',
        'donated_by_blood_banks',
        'verified_by',
    ];

    protected static function booted(){
        static::addGlobalScope('active', function(Builder $builder){
            $builder->where('active_status', true);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bloodBank()
    {
        return $this->belongsTo(BloodBank::class);
    }
}
