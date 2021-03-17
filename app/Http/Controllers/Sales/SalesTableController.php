<?php
namespace App\Http\Controllers\Sales;

use Carbon\Carbon;
use App\Models\Purchase;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class SalesTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function __invoke()
    {
        $sales = Purchase::with(['product', 'transaction']);
        switch (request()->date_option) {
            case 'yesterday':
                $sales = $sales->whereDate('created_at', '=', Carbon::now()->subDay());
                break;
            case 'today':
                $sales = $sales->whereDate('created_at', '=', Carbon::now());
                break;
            case 'this_weekdays':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek()->subDays(2);
                $sales = $sales->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_whole_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $sales = $sales->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $sales = $sales->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                $sales = $sales->whereBetween('created_at', [$start, $end]);
                break;
            default:
                break;
        }
        if(request()->year_to_date) {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::createFromDate(request()->year_to_date);
            $sales = $sales->whereBetween('created_at', [$start, $end]);
        }
        if(request()->from) {
            $sales = $sales->whereDate('created_at', '>=', Carbon::createFromDate(request()->from));
        }
        if(request()->to) {
            $sales = $sales->whereDate('created_at', '<=', Carbon::createFromDate(request()->to));
        }
        return DataTables::of($sales->get())->make(true);
    }
}