<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Deal;
use App\Models\Tax;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_id',
        'company_id',
        'deal_id',
        'status',
        'issue_date',
        'due_date',
        'discount',
        'tax_id',
        'terms',
        'created_by',
    ];

    public static $statues = [
        'Open',
        'Not Paid',
        'Partialy Paid',
        'Paid',
        'Cancelled',
    ];

    public function deal()
    {
        return $this->hasOne('App\Models\Deal', 'id', 'deal_id');
    }

     public static function deals($deal_id)
    {

        $dealArr = explode(',', $deal_id);

        $deal = 0;
        foreach($dealArr as $deal)
        {
            $deals = Deal::find($deal);
            $dealname=$deals->name;
        }
     

        return $dealname;

    }

       public static function taxs($tax_id)
    {

        $taxArr = explode(',', $tax_id);

        $tax = 0;
        foreach($taxArr as $tax)
        {
            $taxs = Tax::find($tax);
            $taxname=$taxs->name;
        }
        
      
        return $taxname;

    }

    public function tax()
    {
        return $this->hasOne('App\Models\Tax', 'id', 'tax_id');
    }

    public function getProducts()
    {
        
        return $this->belongsToMany('App\Models\Product', 'invoice_products', 'invoice_id', 'product_id')->withPivot('id', 'price', 'quantity', 'description');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\InvoicePayment', 'invoice_id', 'id');
    }

    public function getSubTotal()
    {
        $subTotal = 0;
        foreach($this->getProducts as $product)
        {

            $subTotal += $product->pivot->price * $product->pivot->quantity;

        }

        return $subTotal;
    }

    public function getTax()
    {
        if($this->getSubTotal() > 0)
        {
            $tax = (($this->getSubTotal() - $this->discount) * $this->tax->rate) / 100.00;
        }
        else
        {
            $tax = 0;
        }

        return $tax;
    }

    public function getTotal()
    {
        return $this->getSubTotal() - $this->discount + $this->getTax();
    }

    public function getDue()
    {
        $due = 0;
        foreach($this->payments as $payment)
        {
            $due += $payment->amount;
        }

        return $this->getTotal() - $due;
    }

    public static function getInvoiceSummary($invoices)
    {
        $total = 0;

        foreach($invoices as $invoice)
        {
            $total += $invoice->getTotal();
        }

        return \Auth::user()->priceFormat($total);
    }

    public static function getPaymentSummary($payments)
    {
        $total = 0;

        foreach($payments as $payment)
        {
            $total += $payment->amount;
        }

        return \Auth::user()->priceFormat($total);
    }
    public function itemsdata()
    {
        return $this->hasMany('App\Models\Product', 'id', 'id');
    }
}
