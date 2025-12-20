<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'size_id',
        'address_id',
        'status',
        'notes',
        'total_price',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
    public function design()
    {
        return $this->belongsToMany(Design::class, 'design_order')->withPivot('quantity', 'unit_price')->withTimestamps();
    }
    public function designOrders()
    {
        return $this->hasMany(DesignOrder::class);
    }
}
