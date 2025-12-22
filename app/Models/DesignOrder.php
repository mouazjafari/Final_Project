<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignOrder extends Model
{
    protected $table = 'design_order';

    protected $fillable = [
        'order_id',
        'design_id',
        'size_id',
        'quantity',
        'unit_price',
    ];

    /**
     * العلاقة مع الطلب
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * العلاقة مع التصميم
     */
    public function design()
    {
        return $this->belongsTo(Design::class);
    }

    /**
     * العلاقة مع المقاس
     */
    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    /**
     * العلاقة مع خيارات التصميم المختارة
     * Many to Many relationship
     */
    public function options()
    {
        return $this->belongsToMany(
            DesignOption::class,
            'design_option_selected',
            'design_order_id',
            'design_option_id'
        );
    }
}
