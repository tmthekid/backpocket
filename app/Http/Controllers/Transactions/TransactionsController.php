<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use PDF;
use MPDF;

class TransactionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        $transactions = Transaction::all();
        return view('admin.transactions.list', compact('transactions'));
    }

    public function show(Transaction $transaction){
        $extra_info = collect(json_decode($transaction->extra_info, true));
    	return view('admin.transactions.show', compact('transaction', 'extra_info'));
    }

    public function pdf(Transaction $transaction){
        $pdf = PDF::loadView('admin.transactions.invoice', compact('transaction'));
        return $pdf->download('invoice.pdf');
    }

    public function mpdf(Transaction $transaction){
        $config = [
          'title' => $transaction->vendor->name .  " Invoice"
        ];
        $extra_info = collect(json_decode($transaction->extra_info, true));
//        return view('admin.transactions.minvoice', compact('transaction', 'extra_info'));
        $pdf = MPDF::loadView('admin.transactions.minvoice', compact('transaction', 'extra_info'), [], $config);
        return $pdf->download('BackpocketReceipt_'.strtolower($transaction->vendor->name).'_'.$transaction->transaction_no);
    }
}
