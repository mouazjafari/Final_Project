<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $fillable = ['design_id', 'image_path'];

    public function design()
    {
        return $this->belongsTo(Design::class);
    }
}
