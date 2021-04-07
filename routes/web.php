<?php

use App\Category;
use Illuminate\Support\Facades\Route;

// Route::get('imap', 'ImapController@index');

Route::get('/', 'Auth\LoginController@showLoginForm');

Route::get('parse', 'ParseEmailController@index');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/admin/dashboard', 'DashboardController@index')->name('admin.dashboard');
Route::group(['prefix' => 'admin', 'namespace' => 'Transactions', 'name' => 'admin'], function (){
    Route::get('transactions', 'TransactionsController@index')->name('transactions.list');
    Route::post('transactions-datatable', 'TransactionsTableController')->name('transactions.datatable');
    Route::get('transactions/{transaction}', 'TransactionsController@show')->name('transactions.detail');
    Route::get('transactions/pdf/{transaction}', 'TransactionsController@pdf')->name('transactions.pdf');
    Route::get('transactions/mpdf/{transaction}', 'TransactionsController@mpdf')->name('transactions.mpdf');
});

Route::group(['prefix' => 'admin', 'namespace' => 'Vendors', 'name' => 'admin'], function (){
    Route::get('vendors', 'VendorsController@index')->name('vendors.list');
    Route::post('vendors/search', 'VendorsController@search');
    Route::get('vendors/new-this-week', 'VendorsController@week')->name('vendors.week');
    Route::get('vendors/new-this-month', 'VendorsController@month')->name('vendors.month');
    Route::get('vendors/recent', 'VendorsController@recentVendors')->name('vendors.recent');
    Route::post('vendors-datatable', 'VendorsTableController')->name('vendors.datatable');
    Route::get('vendors/{vendor}', 'VendorsController@show')->name('vendors.detail');
});

Route::group(['prefix' => 'admin', 'namespace' => 'Products', 'name' => 'admin'], function (){
    Route::get('products', 'ProductsController@index')->name('products.list');
    Route::post('products-datatable', 'ProductsTableController')->name('products.datatable');
    Route::get('products/{product}', 'ProductsController@show')->name('products.detail');
});

Route::group(['prefix' => 'admin', 'namespace' => 'Sales', 'name' => 'admin'], function (){
    Route::get('sales', 'SalesController@index')->name('sales.list');
    Route::get('sales/top', 'SalesController@topSales')->name('sales.top');
    Route::post('sales-datatable', 'SalesTableController')->name('sales.datatable');
});