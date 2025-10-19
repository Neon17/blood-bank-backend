<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Blog extends Model
{
    protected $fillable = [
        'title',
        'description',
        'author',
        'category',
        'is_featured',
        'tags',
        'status',
        'references',
        'created_by',
    ];

    public function createdBy() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function visibility(): MorphOne {
        return $this->morphOne(Visibility::class, 'visible');
    }
}
