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
        'score_point',
        'status',
    ];

    protected $casts = [
        'score_point' => 'float',
    ];

    public function getVolumeScoreAttribute()
    {
        return $this->score_point ?? 0;
    }

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
