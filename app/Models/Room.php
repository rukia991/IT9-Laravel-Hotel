<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_id',
        'room_status_id',
        'number',
        'capacity',
        'price',
        'view',
        'image',
        'description',
        'is_recommended', // for recommended rooms
    ];

    // Define relationships if needed
    public function type()
    {
        return $this->belongsTo(Type::class);
    }

    public function roomStatus()
    {
        return $this->belongsTo(RoomStatus::class);
    }
}