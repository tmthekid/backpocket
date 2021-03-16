<?php

namespace App\Http\Controllers\Vendors;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class VendorsTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function __invoke(Request $request)
    {

        $vendors = Vendor::all();
//        if($request->order_no!=''){
//            $transactions = Transaction::where('order_no', 'like', "%{$request->order_no}%")->get();
//        } else{
//            $transactions = Transaction::all();
//        }
        return DataTables::of($vendors)
            ->addColumn('actions', function ($vendor) {
                $action = '
                    <div class="btn-group">
                        <a href="'. route('transactions.detail', ['transaction' => $vendor]) .'" class="btn btn-complete"><i class="fa fa-eye"></i>
                        </a>
                        <a href="#!" class="btn btn-success"><i class="fa fa-envelope"></i>
                        </a>
                        <a href="'. route('transactions.pdf', ['transaction' => $vendor->id]) .'" class="btn btn-primary"><i class="fa fa-download"></i>
                        </a>
                    </div>
                ';
                return $action;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
