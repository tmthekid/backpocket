<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $casts = ['created_at' => 'date:Y-m-d'];

    public function product(){
    	return $this->belongsTo(Product::class);
    }

    public function transaction(){
    	return $this->belongsTo(Transaction::class);
    }
}
