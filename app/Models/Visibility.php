<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Visibility extends Model
{
    protected $table = 'visibilities';

    protected $fillable = [
        'name',
        'description',
        'settings',
        'status',
        'latitude',
        'longitude',
        'radius',
        'created_by',
    ];

    public function visible(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
