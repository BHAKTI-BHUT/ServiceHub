<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'phone_number',
        'pickup_location',
        'drop_location',
        'pickup_latitude',
        'pickup_longitude',
        'drop_latitude',
        'drop_longitude',
        'shifting_date',
        'shifting_time',
        'estimated_amount',
        'status',
    ];

    /**
     * Get the customer that owns the booking request.
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the booking created by admin from this request.
     * When admin fills the form and creates a booking linked to this request,
     * the app uses this relationship to show the user their full booking details.
     */
    public function booking()
    {
        return $this->hasOne(Booking::class, 'booking_request_id');
    }
}
