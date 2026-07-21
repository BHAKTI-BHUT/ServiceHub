<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisorLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'supervisor_id',
        'booking_id',
        'latitude',
        'longitude',
    ];

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
