<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdfProduct extends Model
{
    protected $fillable = [
        'mdf_id',
        'product_id',
        'name',
        'price',
        'quantity',
        'description',
        'type',
    ];
}
