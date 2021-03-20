<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vendor extends Model
{
    protected $guarded = [];
    protected $casts = ['created_at' => 'date:m-d-Y'];
    protected $appends = ['short_address'];

    public function getLogoAttribute(){
        return str_replace(' ', '', Str::lower($this->name));
    }

    public function getShortAddressAttribute()
    {
        return Str::limit($this->address, 50);
    }
}
