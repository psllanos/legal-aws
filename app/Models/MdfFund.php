<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MdfFund extends Model
{
    protected $fillable = [
        'mdf_id',
        'amount',
        'payment_id',
        'type',
        'note',
        'date',
        'created_by',
    ];

    public function payment()
    {
        return $this->hasOne('App\Models\Payment', 'id', 'payment_id');
    }
}
