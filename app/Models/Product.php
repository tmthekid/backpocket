<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];
    protected $appends = ['created'];

    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }

    public function getCreatedAttribute()
    {
        return $this->created_at->format('m/d/Y');
    }
}
