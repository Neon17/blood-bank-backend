<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BloodBank extends Model
{
    //
    public $fillable = [
        'name',
        'address',
        'amount_of_blood',
        'phone_number'
    ];
}
