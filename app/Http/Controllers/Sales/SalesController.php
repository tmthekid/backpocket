<?php
namespace App\Http\Controllers\Sales;

use App\Models\Purchase;
use App\Http\Controllers\Controller;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        return view('admin.sales.list');
    }
}
