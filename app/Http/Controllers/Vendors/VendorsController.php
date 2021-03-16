<?php

namespace App\Http\Controllers\Vendors;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VendorsController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $vendors = Vendor::all();
        return view('admin.vendors.list', compact('vendors'));
    }

    public function show(Vendor $vendor){
        return view('admin.vendors.show', compact('vendor'));
    }

    public function search(){
        if(request()->search === '' || request()->search === null) return [];
        return Vendor::where('name', 'LIKE', request()->search.'%')->get();
    }

    public function week(){
        $first = Carbon::now()->startOfWeek();
        $today = Carbon::now();
        return Vendor::whereBetween('created_at', [$first, $today])->get();
    }

    public function month(){
        $first = Carbon::now()->startOfMonth();
        $today = Carbon::now();
        return Vendor::whereBetween('created_at', [$first, $today])->get();
    }

    public function recentVendors(){
        return Vendor::limit(request()->get('length'))->get();
    }
}
