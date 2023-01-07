<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoiceExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data=Invoice::all();

        foreach($data as $k => $invoice)
        {   
        	unset($invoice->id);
        	$deal=Invoice::deals($invoice->deal_id);
        	$tax=Invoice::taxs($invoice->tax_id);

        	 $data[$k]["deal_id"]=$deal;
        	  $data[$k]["tax_id"]=$tax;
        	 $created_bys=User::find($invoice->created_by);
             $created_by=$created_bys->name;
             $data[$k]["created_by"]=$created_by;
        	 
        }

       return $data;


    }

     public function headings(): array
    {
        return [
            "Invoice ID",
            "Deal",
            "Status",
            "issue_date",
            "due_date",
            "discount",
            "tax",
            "terms",
            "created_by",
            "created_at",
            "updated_at",
        ];
    }
}
