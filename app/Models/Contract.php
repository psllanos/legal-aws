<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'name',
        'client_name',
        'subject',
        'value',
        'type',
        'start_date',
        'end_date',
        'notes',
        'description',
        'client_signature',
        'owner_signature',
        'created_by',
        'status',
    ];



    public function contract_type()
    {
        return $this->hasOne('App\Models\ContractType', 'id', 'type');
    }

    public function client()
    {
        return $this->hasOne('App\Models\User', 'id', 'client_name');
    }
    public function files()
    {
        return $this->hasMany('App\Models\ContractAttechment', 'contract_id' , 'id');
    }
    public function comment()
    {
        return $this->hasMany('App\Models\ContractComment', 'contract_id', 'id');
    }
    public function note()
    {
        return $this->hasMany('App\Models\ContractNote', 'contract_id', 'id');
    }


    public static function getContractSummary($contracts)
    {
        $total = 0;

        foreach($contracts as $contract)
        {
            $total += $contract->value;
        }

        return \Auth::user()->priceFormat($total);
    }


    public function ContractAttechment()
    {
        return $this->belongsTo('App\Models\ContractAttechment', 'id', 'contract_id');
    }

    public function ContractComment()
    {
        return $this->belongsTo('App\Models\ContractComment', 'id', 'contract_id');
    }

    public function ContractNote()
    {
        return $this->belongsTo('App\Models\ContractNote', 'id', 'contract_id');
    }
    public static function status()
    {

        $status = [
            'accept' => 'Accept',
            'decline' => 'Decline',

        ];
        return $status;
    }
}
