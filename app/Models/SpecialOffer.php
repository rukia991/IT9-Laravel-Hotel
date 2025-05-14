<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecialOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'discount',
        'valid_from',
        'valid_until',
        'member_status',
        'is_active'
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'discount' => 'decimal:2'
    ];

    public function isValidForMember($memberStatus)
    {
        if (is_null($this->member_status)) {
            return true;
        }

        $statusHierarchy = [
            'Regular' => 0,
            'Silver' => 1,
            'Gold' => 2,
            'Platinum' => 3
        ];

        return $statusHierarchy[$memberStatus] >= $statusHierarchy[$this->member_status];
    }
} 