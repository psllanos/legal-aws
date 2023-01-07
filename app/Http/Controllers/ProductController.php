<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Models\Product;
use App\Models\Utility;
use App\Exports\ProductExport;
use App\Imports\ProductImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(
            [
                'auth',
                'XSS',
            ]
        );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->can('Manage Products'))
        {
            $products = Product::where('created_by', '=', \Auth::user()->ownerId())->get();

            return view('products.index')->with('products', $products);
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(\Auth::user()->can('Create Product'))
        {
            $customFields = CustomField::where('module', '=', 'product')->get();

            return view('products.create', compact('customFields'));
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if(\Auth::user()->can('Create Product'))
        {

            $Validator = [
                'name' => 'required|max:100',
                'price' => 'required|min:0',
            ];

            if($request->image)
            {
                $Validator['image'] = 'required|image';
            }

            $validator = \Validator::make($request->all(), $Validator);

            if($validator->fails())
            {
                $messages = $validator->getMessageBag();

                return redirect()->route('products.index')->with('error', $messages->first());
            }

            $product              = new Product();
            $product->name        = $request->name;
            $product->price       = $request->price;
            $product->description = $request->description;
            $product->type        = $request->type;
            $product->created_by  = \Auth::user()->ownerId();
            $product->save();

            if($request->image)
            {
                $filenameWithExt = $request->file('image')->getClientOriginalName();
                $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                $extension       = $request->file('image')->getClientOriginalExtension();
                $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                $settings = Utility::getStorageSetting();

                $dir        = 'product/';
                $url = '';
                $path = Utility::upload_file($request,'image',$filenameWithExt,$dir,[]);

                if($path['flag'] == 1){
                    $url = $path['url'];
                    $product->image = $url;
                    $product->save();
                }else{
                    return redirect()->back()->with('error', __($path['msg']));
                }
            }

            CustomField::saveData($product, $request->customField);

            return redirect()->route('products.index')->with('success', __('Product successfully created!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return redirect()->route('products.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        if(\Auth::user()->can('Edit Product'))
        {
            if($product->created_by == \Auth::user()->ownerId())
            {
                $product->customField = CustomField::getData($product, 'product');
                $customFields         = CustomField::where('module', '=', 'product')->get();

                return view('products.edit', compact('product', 'customFields'));
            }
            else
            {
                return response()->json(['error' => __('Permission Denied.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        if(\Auth::user()->can('Edit Product'))
        {
            if($product->created_by == \Auth::user()->ownerId())
            {
                $Validator = [
                    'name' => 'required|max:100',
                    'price' => 'required|min:0',
                ];

                if($request->image)
                {
                    $Validator['image'] = 'required|image';
                }

                $validator = \Validator::make($request->all(), $Validator);

                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('products.index')->with('error', $messages->first());
                }

                $product->name        = $request->name;
                $product->price       = $request->price;
                $product->description = $request->description;
                $product->type        = $request->type;
                $product->save();

                if($request->image)
                {
                    $filenameWithExt = $request->file('image')->getClientOriginalName();
                    $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    $extension       = $request->file('image')->getClientOriginalExtension();
                    $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                    $settings = Utility::getStorageSetting();

                    $dir        = 'product/';



                    // if(!file_exists($dir))
                    // {
                    //     mkdir($dir, 0777, true);
                    // }
                    $url = '';
                    // $path = $request->file('image')->storeAs('uploads/avatar/', $fileNameToStore);
                    // dd($path);
                    $path = Utility::upload_file($request,'image',$filenameWithExt,$dir,[]);

                    if($path['flag'] == 1){
                        $url = $path['url'];
                        $product->image = $url;
                        $product->save();
                    }else{
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                }

                CustomField::saveData($product, $request->customField);

                return redirect()->route('products.index')->with('success', __('Product successfully updated!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Product $product
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if(\Auth::user()->can('Delete Product'))
        {
            if($product->created_by == \Auth::user()->ownerId())
            {
                \File::delete(storage_path('product/' . $product->image));

                $product->delete();

                return redirect()->route('products.index')->with('success', __('Product successfully deleted!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Permission Denied.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

     public function fileExport()
    {

        $name = 'product_' . date('Y-m-d i:h:s');
        $data = Excel::download(new ProductExport(), $name . '.xlsx');  ob_end_clean();


        return $data;
    }

       public function fileImportExport()
    {
        return view('products.import');
    }

    public function fileImport(Request $request)
    {
        $rules = [
            'file' => 'required|mimes:csv,txt,xlsx',
        ];

        $validator = \Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $products = (new ProductImport())->toArray(request()->file('file'))[0];

        $totalproduct = count($products) - 1;

        $errorArray    = [];
        for($i = 1; $i <= count($products) - 1; $i++)
        {
            $product = $products[$i];
            $productByname = Product::where('name', $product[0])->first();


            if(!empty($productByname))
            {
                $productData = $productByname;
            }
            else
            {
                $productData = new Product();

            }


            $productData->name             = $product[0];
            $productData->price            = $product[1];
            $productData->description         = $product[2];
            $productData->type          = $product[3];
            $productData->created_by        = \Auth::user()->ownerId();



            if(empty($productData))
            {
                $errorArray[] = $productData;
            }
            else
            {
                $productData->save();
            }
        }

        $errorRecord = [];
        if(empty($errorArray))
        {
            $data['status'] = 'success';
            $data['msg']    = __('Record successfully imported');
        }
        else
        {
            $data['status'] = 'error';
            $data['msg']    = count($errorArray) . ' ' . __('Record imported fail out of' . ' ' . $totalproduct . ' ' . 'record');


            foreach($errorArray as $errorData)
            {

                $errorRecord[] = implode(',', $errorData);

            }

            \Session::put('errorArray', $errorRecord);
        }

        return redirect()->back()->with($data['status'], $data['msg']);
    }
}
