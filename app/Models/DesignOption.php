<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Translatable\HasTranslations;

class DesignOption extends Model
{
    use HasFactory, Notifiable, HasTranslations;
    protected $table = 'design_options';
    protected $fillable = [
        'name',
        'type',
    ];
    public array $translatable = ['name'];

    public function designs()
    {
        return $this->belongsToMany(Design::class);
    }
    public function selectedInOrders()
    {
        return $this->belongsToMany(DesignOptionSelected::class, 'design_option_selected', 'design_option_id', 'design_order_id');
    }
}
