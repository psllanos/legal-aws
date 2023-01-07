<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Mdf extends Model
{
    protected $fillable = [
        'mdf_id',
        'user_id',
        'status',
        'type',
        'sub_type',
        'date',
        'amount',
        'description',
        'is_complete',
        'created_by',
    ];

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function statusDetail()
    {
        return $this->hasOne('App\Models\MdfStatus', 'id', 'status');
    }

    public function typeDetail()
    {
        return $this->hasOne('App\Models\MdfType', 'id', 'type');
    }

    public function subTypeDetail()
    {
        return $this->hasOne('App\Models\MdfSubType', 'id', 'sub_type');
    }

    public static function getMdfSummary($mdfs, $without_format = false)
    {
        $total = 0;

        foreach($mdfs as $mdf)
        {
            $total += $mdf->amount;
        }

        if($without_format == false)
        {
            return Auth::user()->priceFormat($total);
        }
        else
        {
            return $total;
        }
    }

    public function getProducts()
    {
        return $this->hasMany('App\Models\MdfProduct', 'mdf_id', 'id');
    }

    public function getSubTotal()
    {
        $subTotal = 0;
        foreach($this->getProducts as $product)
        {
            $subTotal += ($product->quantity > 0) ? $product->price * $product->quantity : $product->price;
        }

        return $subTotal;
    }

    public function getDue()
    {
        return $this->getFundAmt() - $this->getSubTotal();
    }

    public function getFundAmt()
    {
        // Get Funds Amount
        $funds       = $this->funds;
        $total_funds = 0;
        foreach($funds as $fund)
        {
            $total_funds += $fund->amount;
        }

        return $total_funds;
    }

    public function getTotal()
    {
        return $this->amount - $this->getSubTotal();
    }

    public function approvedAmt()
    {
        return $this->hasOne('App\Models\MdfFund', 'mdf_id', 'id')->where('type', 'LIKE', 'approved');
    }

    public function funds()
    {
        return $this->hasMany('App\Models\MdfFund', 'mdf_id', 'id');
    }
}
