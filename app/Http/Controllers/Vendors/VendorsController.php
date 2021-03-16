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
}
