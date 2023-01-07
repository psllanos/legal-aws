<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data=Product::all();
        
        
        foreach($data as $k => $product)
        {
        	$data[$k]["price"]=\Auth::user()->priceFormat($product->price);
            $created_bys=User::find($product->created_by);
             $created_by=$created_bys->name;
             $data[$k]["created_by"]=$created_by;

        }
       
        
        return $data;
    }
    public function headings(): array
    {
        return [
            "Product Id",
            "Name",
            "Price",
            "Description",
            "image",
            "type",
            "created_by",
            "created_at",
            "updated_at",
        ];
    }
}
