<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'comment',
        'rating',
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function user()
    {
        return $this->belongsToMany(User::class);
    }
}
