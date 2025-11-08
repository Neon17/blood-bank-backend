<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BloodInventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'blood_bank_id',
        'blood_type',
        'quantity'
    ];

    protected $casts = [
        'quantity' => 'integer'
    ];

    /**
     * Blood types constants
     */
    public const BLOOD_TYPES = [
        'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'
    ];

    /**
     * Get the blood bank that owns the inventory.
     */
    public function bloodBank(): BelongsTo
    {
        return $this->belongsTo(BloodBank::class);
    }
}