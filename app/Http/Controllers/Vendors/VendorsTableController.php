<?php

namespace App\Http\Controllers\Vendors;

use Carbon\Carbon;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class VendorsTableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function __invoke(Request $request)
    {
        $vendors = Vendor::query();
        switch (request()->date_option) {
            case 'yesterday':
                $vendors = $vendors->whereDate('created_at', '=', Carbon::now()->subDay());
                break;
            case 'today':
                $vendors = $vendors->whereDate('created_at', '=', Carbon::now());
                break;
            case 'this_weekdays':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek()->subDays(2);
                $vendors = $vendors->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_whole_week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $vendors = $vendors->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $vendors = $vendors->whereBetween('created_at', [$start, $end]);
                break;
            case 'this_year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                $vendors = $vendors->whereBetween('created_at', [$start, $end]);
                break;
            default:
                break;
        }
        if(request()->year_to_date) {
            $start = Carbon::now()->startOfYear();
            $end = Carbon::createFromDate(request()->year_to_date);
            $vendors = $vendors->whereBetween('created_at', [$start, $end]);
        }
        if(request()->from) {
            $vendors = $vendors->whereDate('created_at', '>=', Carbon::createFromDate(request()->from));
        }
        if(request()->to) {
            $vendors = $vendors->whereDate('created_at', '<=', Carbon::createFromDate(request()->to));
        }
        return DataTables::of($vendors)->make(true);
    }
}
