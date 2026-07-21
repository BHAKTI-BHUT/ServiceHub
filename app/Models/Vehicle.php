<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_name',
        'vehicle_capacity_score',
        'status',
    ];

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
