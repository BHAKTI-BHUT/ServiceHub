<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'item_size_id',
        'status',
    ];

    public function size()
    {
        return $this->belongsTo(ItemSize::class, 'item_size_id');
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_items')
            ->withPivot('quantity', 'calculated_volume_score')
            ->withTimestamps();
    }
}
