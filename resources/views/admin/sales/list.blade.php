@extends('admin.layouts.master')
@section('title', 'Sales')
@section('page-css')
    <style>
        .dataTables_filter {
            display: none;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid container-fixed-lg">
        <div class="card card-default">
            <div class="card-header">
                <div class="card-title">
                    <h5><strong>Sales</strong></h5>
                </div>
            </div>
            <div class="row px-4">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="from" class="control-label">From</label>
                        <input type="date" id="from" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="to" class="control-label">To</label>
                        <input type="date" id="to" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="year_to_date" class="control-label">Year to date</label>
                        <input type="date" id="year_to_date" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_options" class="control-label">Date Options</label>
                        <select name="date_options" id="date_options" class="form-control">
                            <option value="">Pick an option</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="today">Today</option>
                            <option value="this_weekdays">This Weekdays</option>
                            <option value="this_whole_week">This Whole Week</option>
                            <option value="this_month">This Month</option>
                            <option value="this_year">This Year</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-hover table-condensed table-responsive" id="salesDatatable">
                    <thead>
                    <tr>
                        <th>Transaction</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('page-js')
    <script>
        $(document).ready(function (e) {
            var table = $('#salesDatatable');
            var sales_datatable = table.DataTable({
                "columnDefs": [
                    { "width": "20%", "targets": 0 },
                    { "width": "20%", "targets": 1 },
                    { "width": "20%", "targets": 2 },
                    { "width": "20%", "targets": 3 },
                    { "width": "20%", "targets": 4 }
                ],
                "serverSide": true,
                "sDom": '<"H"lfr>t<"F"ip>',
                "method": "post",
                "destroy" : true,
                "pageLength": 10,
                "sPaginationType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "ajax": {
                    "url": "{{ route('sales.datatable') }}",
                    "type": "POST",
                    "data": function (d) {
                        d.from = $('#from').val();
                        d.to = $('#to').val();
                        d.date_option = $('#date_options').val();
                        d.year_to_date = $('#year_to_date').val();
                    }
                },
                "order": [[ 0, "desc" ]],
                "columns": [
                    {data: 'transaction.transaction_no'},
                    {data: 'product.name'},
                    {data: 'price'},
                    {data: 'quantity'},
                    {data: 'created_at'}
                ],
            });
            $('#from').change( function() {
                sales_datatable.draw();
            });
            $('#to').change( function() {
                sales_datatable.draw();
            });
            $('#date_options').change( function() {
                sales_datatable.draw();
            });
            $('#year_to_date').change( function() {
                sales_datatable.draw();
            });
            $('#salesDatatable thead tr').clone(true).appendTo('#salesDatatable thead');
            $('#salesDatatable thead tr:eq(1) th').each( function (i) {
                $(this).removeClass('sorting');
                var title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="Search '+title+'" />');
                $('input', this).on('keyup change click', function(e) {
                    e.stopPropagation();
                    if (sales_datatable.column(i).search() !== this.value) {
                        sales_datatable
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            });
        });
    </script>
@endsection