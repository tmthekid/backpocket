<?php

namespace App\Http\Controllers\Products;

use Carbon\Carbon;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class ProductsTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function __invoke(Request $request)
    {
        $products = Product::query();
        if($request->vendor_id!=''){
            $products = $products->where('vendor_id', "{$request->vendor_id}");
        } 
        if($request->product_name) {
            $products = $products->where('name', 'LIKE', '%'.$request->product_name.'%');
        }
        switch (request()->date_option) {
            case 'yesterday':
                $products = $products->whereDate('created_at', '=', Carbon::now()->subDay());
                break;
            case 'today':
                $products = $products->whereDate('created_at', '=', Carbon::now());
                break;
            case 'this_weekdays':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek()->subDays(2);
                $products = $products->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_whole_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $products = $products->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $products = $products->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                $products = $products->whereBetween('created_at', [$start, $end]);
                break;
            default:
                break;
        }
        if(request()->year_to_date) {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::createFromDate(request()->year_to_date);
            $products = $products->whereBetween('created_at', [$start, $end]);
        }
        if(request()->from) {
            $products = $products->whereDate('created_at', '>=', Carbon::createFromDate(request()->from));
        }
        if(request()->to) {
            $products = $products->whereDate('created_at', '<=', Carbon::createFromDate(request()->to));
        }
        return DataTables::of($products->get())
            ->addColumn('vendor', function ($product) {
                return $product->vendor->name;
            }) ->addColumn('vendor_id', function ($product) {
                return $product->vendor->id;
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
