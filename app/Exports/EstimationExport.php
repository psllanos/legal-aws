<?php

namespace App\Exports;

use App\Models\Estimation;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EstimationExport implements FromCollection ,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $data=Estimation::all();

        foreach($data as $k => $estimation)
        {
        	unset($estimation->id);
        	$client=Estimation::clients($estimation->client_id);
        	$tax=Estimation::taxs($estimation->tax_id);
        	 $data[$k]["client_id"]=$client;
        	 $data[$k]["tax_id"]=$tax;
              $created_bys=User::find($estimation->created_by);
             $created_by=$created_bys->name;
             $data[$k]["created_by"]=$created_by;
             $data[$k]["status"]=Estimation::$statues[$estimation->status];
        	

        }
      return $data;	
    }

     public function headings(): array
    {
        return [
            "Estimation Id",
            "Client",
            "Status",
            "issue_date",
            "discount",
            "tax",
            "terms",
            "created_by",
            "created_at",
            "updated_at",
        ];
    }
}
