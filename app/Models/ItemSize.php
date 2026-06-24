<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemSize extends Model
{
    protected $fillable = ['size_name', 'volume_score', 'status'];

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
