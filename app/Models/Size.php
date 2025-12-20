<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $fillable = ['name'];

    public function designs()
    {
        return $this->belongsToMany(Design::class);
    }
    public function designorders()
    {
        return $this->belongsToMany(DesignOrder::class);
    }
}
