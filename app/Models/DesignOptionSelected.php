<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignOptionSelected extends Model
{
    protected $fillable = [
        'design_order_id',
        'design_option_id',
    ];

    public function designOrder()
    {
        return $this->belongsTo(DesignOrder::class);
    }

    public function designOption()
    {
        return $this->belongsTo(DesignOption::class);
    }
}
