<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddOn extends Model
{
    use HasFactory;

    protected $fillable = [
        'addon_name',
        'price',
        'status',
    ];

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_add_ons')
            ->withPivot('price')
            ->withTimestamps();
    }
}
