<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'icon'
    ];

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'facility_room');
    }
}
