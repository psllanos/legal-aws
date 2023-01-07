<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    protected $fillable = [
        'invoice_id',
        'product_id',
        'price',
        'quantity',
        'description',
    ];

     
}
