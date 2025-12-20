<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    protected $fillable = ['user_id', 'name', 'description', 'price', 'quantity'];
    public array $translatable = ['name', 'description'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'design_size');
    }
    public function designOptions()
    {
        return $this->belongsToMany(
            DesignOption::class,
            'design_design_options',
            'design_id',
            'design_options_id'
        );
    }
    public function images()
    {
        return $this->hasMany(Image::class);
    }
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'design_order')->withPivot('quantity', 'unit_price')->withTimestamps();
    }
}
