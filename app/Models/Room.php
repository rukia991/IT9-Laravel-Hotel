<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Image;
use App\Models\Facility;
use App\Models\Customer;
use App\Models\Transaction;



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
    public function images()
    {
        return $this->hasMany(Image::class);
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'facility_room');
    }

    public function currentGuest()
    {
        return $this->hasOneThrough(
            Customer::class,
            Transaction::class,
            'room_id', // Foreign key on transactions table
            'id', // Foreign key on customers table
            'id', // Local key on rooms table
            'customer_id' // Local key on transactions table
        )->where(function($query) {
            $query->whereHas('transactions', function($q) {
                $q->where('room_id', $this->id)
                  ->where('status', 'Approved')
                  ->where('check_in', '<=', now())
                  ->where('check_out', '>', now());
            });
        });
    }

    // Add a new relationship for current transaction
    public function currentTransaction()
    {
        return $this->hasOne(Transaction::class)
            ->where('status', 'Approved')
            ->where('check_in', '<=', now())
            ->where('check_out', '>', now());
    }
}