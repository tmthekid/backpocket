<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProductsTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function __invoke(Request $request)
    {
        $products = Product::all();
        if($request->vendor_id!=''){
            $products = Product::where('vendor_id', "{$request->vendor_id}")->get();
        } else{
            $products = Product::all();
        }
        return DataTables::of($products)
            ->addColumn('vendor', function ($product) {
                return $product->vendor->name;
            })
            ->addColumn('actions', function ($product) {
                $action = '
                    <div class="btn-group">
                        <a href="'. route('products.detail', ['product' => $product]) .'" class="btn btn-complete"><i class="fa fa-eye"></i>
                        </a>
                        <a href="#!" class="btn btn-success"><i class="fa fa-envelope"></i>
                        </a>
                      
                    </div>
                ';
                return $action;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
