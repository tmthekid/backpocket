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

    public function topSales()
    {
        return Purchase::with(['product' => function($query){
            $query->select('id', 'name', 'created_at');
        }, 'transaction' => function($query){
            $query->select('id', 'transaction_no');
        }])->limit(request()->get('length'))->orderBy('price', 'desc')->get();
    }
}
