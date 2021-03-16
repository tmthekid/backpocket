<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $products = Product::all();
        $vendors = Vendor::all();
        return view('admin.products.list', compact('products', 'vendors'));
    }

    public function show(Product $product){
        return view('admin.products.show', compact('product'));
    }
}
