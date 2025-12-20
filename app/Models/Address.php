<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Address extends Model
{
    protected $fillable = ['user_id', 'city_id', 'area', 'street', 'Langitude', 'Longitude', 'notes'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id'); 
    }
}
