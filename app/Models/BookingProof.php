<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingProof extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'file_path',
        'type',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
