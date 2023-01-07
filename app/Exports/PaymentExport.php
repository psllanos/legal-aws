<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;

class PaymentExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data=Payment::all();

        foreach($data as $k => $payment)
        {
        	unset($estimation->id);
        	$client=Estimation::clients($estimation->client_id);
        	$tax=Estimation::taxs($estimation->tax_id);
        	 $data[$k]["client_id"]=$client;
        	 $data[$k]["tax_id"]=$tax;

        	

        }
    }
}
