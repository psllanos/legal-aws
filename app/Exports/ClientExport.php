<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data= User::where('type','Client')->where('created_by',\Auth::user()->ownerId())->get();
    
        foreach($data as $k => $client)
        {
            
        	
        	 unset($client->email_verified_at,$client->avatar, $client->job_title, $client->default_pipeline, $client->plan, $client->plan_expire_date,$client->requested_plan,$client->payment_subscription_id,$client->is_trial_done,$client->is_plan_purchased,$client->interested_plan_id,$client->is_register_trial,$client->delete_status,$client->remember_token,$client->dark_mode,$client->active_status,$client->password);
             $created_bys=User::find($client->created_by);
             $created_by=$created_bys->name;
             $data[$k]["created_by"]=$created_by;
        	  
        }
       

        return $data;

    }

    public function headings(): array
    {
        return [
        "Id",
        "Name",
        "Email",
        "Type",
        "Lang",
        "Created_by",
        "is_active",
        "created_at",
        "updated_at",
        "messenger_color",
        ];
    }
}
