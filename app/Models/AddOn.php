<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddOn extends Model
{
    use HasFactory;

    protected $fillable = [
        'addon_category_id',
        'addon_name',
        'price',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(AddOnCategory::class, 'addon_category_id');
    }

    public function bookings()
    {
        return $this->belongsToMany(Booking::class, 'booking_add_ons')
            ->withPivot('price')
            ->withTimestamps();
    }
}
