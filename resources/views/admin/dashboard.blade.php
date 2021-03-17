@extends('admin.layouts.master')
@section('title', 'Dashboard')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-3 col-lg-6 col-sm-12">
                @include('admin.components.dashboard-card', ['id' => 'week', 'title' => "Week"])
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-12">
                @include('admin.components.dashboard-card', ['id' => 'month', 'title' => "Month"])
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-12">
                <div class="card" style="background: #10cfbd; height: 130px; box-shadow: rgba(0, 0, 0, 0.2) 0px 4px 6px -1px, rgba(0, 0, 0, 0.4) 0px 2px 4px -1px;">
                    <div class="card-body">
                        <h3 class="card-title" style="color: #fff">Search Vendor</h3>
                        <form method="POST">
                            <input class="form-control" name="search" id="searchVendor">
                            {{csrf_field()}}
                        </form>
                        <div id="vendorFilterList"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-12">
                <div class="card" style="background: #10cfbd; height: 130px; box-shadow: rgba(0, 0, 0, 0.2) 0px 4px 6px -1px, rgba(0, 0, 0, 0.4) 0px 2px 4px -1px;">
                    <div class="card-body">
                        <h3 class="card-title" style="color: #fff">Reports</h3>
                        <a style="color: #fff; font-size: 1.2rem; margin-right: 1.2rem;" href="{{route('sales.list')}}"><i class="fa fa-money mr-2" aria-hidden="true"></i>Sales</a>
                        <a style="color: #fff; font-size: 1.2rem" href="{{route('transactions.list')}}"><i class="fa fa-paper-plane mr-2" aria-hidden="true"></i>Transactions</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-4 col-lg-12">
                @include('admin.components.vendor-dashboard-table')
            </div>
            <div class="col-xl-4 col-lg-12">
                @include('admin.components.sales-dashboard-table')
            </div>
        </div>
    </div>
@endsection
@section('page-js')
    <script>
        $(document).ready(function (e) {
            var table = $('#vendorsTable');
            table.DataTable({
                "searching": false,
                "lengthMenu": [ 5, 10, 25, 50, 75, 100 ],
                "order": [[ 0, "desc" ]],
                "info": false,
                "pageLength": 5,
                "ajax": {
                    url: "{{ route('vendors.recent') }}",
                    dataSrc: ""
                },
                "columns": [
                        {data: 'id'},
                        {data: 'name'},
                        {data: 'email'},
                        {data: 'address'},
                        {data: 'store_no'},
                        {data: 'tax_no'}
                    ]
            });
        });
        $(document).ready(function (e) {
            var table = $('#salesTable');
            table.DataTable({
                "searching": false,
                "info": false,
                "lengthMenu": [ 5, 10, 25, 50, 75, 100 ],
                "pageLength": 5,
                "ajax": {
                    url: "{{ route('sales.top') }}",
                    dataSrc: ""
                },
                "columns": [
                        {data: 'transaction.transaction_no'},
                        {data: 'product.name'},
                        {data: 'price'},
                        {data: 'quantity'},
                        {data: 'created_at'}
                    ]
            });
        });
    </script>
@endsection