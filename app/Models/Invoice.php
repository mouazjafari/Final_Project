<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'order_id',
        'total',
        'pdf_url'
    ];
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
