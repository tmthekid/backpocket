<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class TransactionsTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function __invoke(Request $request)
    {

        if($request->order_no!=''){
            $transactions = Transaction::where('order_no', 'like', "%{$request->order_no}%")->get();
        } else{
            $transactions = Transaction::all();
        }
        return DataTables::of($transactions)
            ->addColumn('transaction_date', function ($transaction) {
                return date('m-d-Y', strtotime($transaction->transaction_date));
            })
            ->addColumn('transaction_time', function ($transaction) {
                return date('h:i:s A', strtotime($transaction->transaction_date));
            })
            ->addColumn('vendor_name', function ($transaction) {
                return $transaction->vendor->name;
            })
            ->addColumn('vendor_email', function ($transaction) {
                return $transaction->vendor->email;
            })
            ->addColumn('actions', function ($transaction) {
                $action = '
                    <div class="btn-group">
                        <a href="'. route('transactions.detail', ['transaction' => $transaction]) .'" class="btn btn-complete"><i class="fa fa-eye"></i>
                        </a>
                        <a href="#!" class="btn btn-success"><i class="fa fa-envelope"></i>
                        </a>
                        <a href="'. route('transactions.mpdf', ['transaction' => $transaction->id]) .'" class="btn btn-primary"><i class="fa fa-download"></i>
                        </a>
                    </div>
                ';
                return $action; //$transaction->action_buttons;
            })
            ->rawColumns(['actions'])
            ->make(true);
    }
}
