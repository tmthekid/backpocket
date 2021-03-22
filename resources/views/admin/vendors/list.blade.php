@extends('admin.layouts.master')
@section('title', 'Vendors List')
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
            <div class="card-header separator">
                <div class="card-title">
                    <h5><strong>Vendors</strong></h5>
                </div>
            </div>
            <div class="card-body p-t-20">
                {{--<form action="">--}}
                    {{--<div class="row justify-content-left">--}}
                        {{--<div class="col-md-3">--}}
                            {{--<div class="form-group" style="display: inline-block">--}}

                                {{--<div class="row">--}}
                                    {{--<div class="col-md-5">--}}
                                        {{--<label>Order No</label>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-md-7">--}}
                                        {{--<input type="text" class="form-control" id="filter_order_no">--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</form>--}}

                {{--<hr>--}}
                <div class="row mb-3">
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
                <table class="table table-hover table-condensed table-responsive-block table-responsive"
                        id="vendorsTable">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Store No.</th>
                        <th>Tax No.</th>
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
            var table = $('#vendorsTable');
            $.fn.dataTable.ext.errMode = 'none';
            var vendor_datatable = table.DataTable({
                "serverSide": true,
                "sDom": '<"H"lfr>t<"F"ip>',
                "destroy": true,
                "pageLength": 10,
                "sPaginationType": "full_numbers",
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "ajax": {
                    "url": "{{ route('vendors.datatable') }}",
                    "method": "POST",
                    'data': function(data){
                        data.from = $('#from').val();
                        data.to = $('#to').val();
                        data.date_option = $('#date_options').val();
                        data.year_to_date = $('#year_to_date').val();
                    }
                },
                "order": [[ 0, "asc" ]],
                "columns": [
                    { "data": "name", "name": "name",
                        fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                            $(nTd).html("<a style='color: #0090d9' href='/admin/vendors/"+oData.id+"'>"+oData.name+"</a>");
                        }
                    },
                    {data: 'email', name: 'email'},
                    {data: 'short_address', name: 'short_address'},
                    {data: 'store_no', name: 'store_no'},
                    {data: 'tax_no', name: 'tax_no'},
                    {data: 'created_at', name: 'created_at'}
                ]
            });
            $('#from').change( function() {
                vendor_datatable.draw();
            });
            $('#to').change( function() {
                vendor_datatable.draw();
            });
            $('#date_options').change( function() {
                vendor_datatable.draw();
            });
            $('#year_to_date').change( function() {
                vendor_datatable.draw();
            });
            $('#vendorsTable thead tr').clone(true).appendTo('#vendorsTable thead');
            $('#vendorsTable thead tr:eq(1) th').each( function (i) {
                $(this).removeClass('sorting');
                var title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="Search '+title+'" />');
                $('input', this).on('keyup change click', function(e) {
                    e.stopPropagation();
                    if (vendor_datatable.column(i).search() !== this.value) {
                        vendor_datatable
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            });
        });
    </script>

@endsection
