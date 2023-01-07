<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'category_id',
        'description',
        'amount',
        'date',
        'deal_id',
        'user_id',
        'attachment',
        'created_by',
    ];

    public function category()
    {
        return $this->hasOne('App\Models\ExpenseCategory', 'id', 'category_id');
    }

    public function deal()
    {
        return $this->hasOne('App\Models\Deal', 'id', 'deal_id');
    }

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public static function getExpenseSummary($expenses)
    {
        $total = 0;

        foreach($expenses as $expense)
        {
            $total += $expense->amount;
        }

        return \Auth::user()->priceFormat($total);
    }
}
