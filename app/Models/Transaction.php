<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    
    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }

    public function purchase(){
        return $this->hasMany(Purchase::class);
    }
}
