<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'size_id',
        'address_id',
        'coupon_id',        // ✅ جديد
        'discount_amount',  // ✅ جديد
        'status',
        'notes',
        'total_price',
    ];
    protected $casts = [
        'discount_amount' => 'decimal:2', // ✅ جديد
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
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
    // ✅ جديد - العلاقة مع الكوبون
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
