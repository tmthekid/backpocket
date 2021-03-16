@extends('admin.layouts.master')

@section('title', 'Transactions List')

@section('page-css')

@endsection

@section('content')

    <!-- START CONTAINER FLUID -->
    <div class=" container-fluid   container-fixed-lg">
        <!-- START card -->
        <div class="card card-default">
            <div class="card-header separator">
                <div class="card-title">
                    <h5><strong>Transactions list</strong></h5>
                </div>
            </div>
            <div class="card-body p-t-20">
                <form action="">
                    <div class="row justify-content-left">
                        <div class="col-md-3">
                            <div class="form-group" style="display: inline-block">

                                <div class="row">
                                    <div class="col-md-5">
                                        <label>Order No</label>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" class="form-control" id="filter_order_no">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--<div class="col-md-3">--}}
                            {{--<!-- <div class="input-group">--}}
                                {{--<div class="input-group-prepend">--}}
                                    {{--<span class="input-group-text"><i class="fa fa-calendar"></i></span>--}}
                                {{--</div>--}}
                                {{--<input type="text" name="reservation" id="daterangepicker"--}}
                                    {{--class="form-control" value="08/01/2013 1:00 PM - 08/01/2013 1:30 PM">--}}
                            {{--</div> -->--}}
                            {{--<div class="row">--}}
                                {{--<div class="col-md-4">--}}
                                    {{--<label>Quick Date</label>--}}
                                {{--</div>--}}
                                {{--<div class="col-md-7">--}}
                                    {{--<select class="form-control">--}}
                                        {{--<!-- <option value="" selected disabled>Quick Date</option> -->--}}
                                        {{--<option value="">Today</option>--}}
                                        {{--<option value="">This Week</option>--}}
                                        {{--<option value="">This Month</option>--}}
                                        {{--<option value="">This Year</option>--}}
                                    {{--</select>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-md-3">--}}
                            {{--<div class="form-group">--}}
                                {{--<div class="row">--}}
                                    {{--<div class="col-md-4">--}}
                                        {{--<label>Status</label>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-md-7">--}}
                                        {{--<select class="form-control">--}}
                                            {{--<!-- <option value="" selected disabled>Status</option> -->--}}
                                            {{--<option value="">Pending</option>--}}
                                            {{--<option value="">Active</option>--}}
                                        {{--</select>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-md-3">--}}
                            {{--<div class="form-group" style="display: inline-block">--}}

                                {{--<div class="row">--}}
                                    {{--<div class="col-md-5">--}}
                                        {{--<label>Store</label>--}}
                                    {{--</div>--}}
                                    {{--<div class="col-md-7">--}}
                                        {{--<input type="text" class="form-control">--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    </div>
                </form>

                <hr>
                <div class="">
                    <table class="table table-hover table-condensed table-responsive-block table-responsive"
                           id="tableTransactions">
                        <thead>
                        <tr>
                            <!-- NOTE * : Inline Style Width For Table Cell is Required as it may differ from user to user
                            Comman Practice Followed
                            -->
                            <th style="width:10%;">ID</th>
                            <th style="width: 12%;">Transaction Date</th>
                            <th style="width: 12%;">Transaction Time</th>
                            <th style="width: 10%;">Order no</th>
                            <th style="width: 13%;">Vendor</th>
                            <th style="width: 13%;">Vendor Email</th>
                            <th style="width: 10%;">Amount</th>
                            <th style="width: 20%;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>

                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END card -->
    </div>
    <!-- END CONTAINER FLUID -->

@endsection

@section('page-js')

    <script>
        $(document).ready(function (e) {

            var table = $('#tableTransactions');
            var trans_datatable = table.DataTable({
                "processing": true,
                "serverSide": true,
                "sDom": "<t><'row'<p i>>",
                "destroy": true,
                "scrollCollapse": true,
                "oLanguage": {
                    "sLengthMenu": "_MENU_ ",
                    "sInfo": "Showing <b>_START_ to _END_</b> of _TOTAL_ entries"
                },
                "iDisplayLength": 5,
                "method": "post",
                "ajax": {
                    "url": "{{ route('transactions.datatable') }}",
                    "type": "POST",
                    'data': function(data){
                        data.order_no = $('#filter_order_no').val();
                    }
                },
                "order": [[ 0, "asc" ]],
                "columns": [
                    // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'id', name: 'id'},
                    {data: 'transaction_date', name: 'transaction_date'},
                    {data: 'transaction_time', name: 'transaction_time'},
                    {data: 'order_no', name: 'order_no'},
                    {data: 'vendor_name', name: 'vendor_name'},
                    {data: 'vendor_email', name: 'vendor_email'},
                    {data: 'total', name: 'total'},
                    {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ]
            });

            $(document).on('keyup', '#filter_order_no', function () {
                console.log($('#filter_order_no').val());
               trans_datatable.draw();
            });

            // var _format = function (d) {
            //     // `d` is the original data object for the row
            //     return '<table class="table table-inline">' +
            //         '<tr>' +
            //         '<td>Learn from real test data <span class="label label-important">ALERT!</span></td>' +
            //         '<td>USD 1000</td>' +
            //         '</tr>' +
            //         '<tr>' +
            //         '<td>PSDs included</td>' +
            //         '<td>USD 3000</td>' +
            //         '</tr>' +
            //         '<tr>' +
            //         '<td>Extra info</td>' +
            //         '<td>USD 2400</td>' +
            //         '</tr>' +
            //         '</table>';
            // }

            // // Add event listener for opening and closing details
            // $('#tableTransactions tbody').on('click', 'tr', function () {
            //     //var row = $(this).parent()
            //     if ($(this).hasClass('shown') && $(this).next().hasClass('row-details')) {
            //         $(this).removeClass('shown');
            //         $(this).next().remove();
            //         return;
            //     }
            //     var tr = $(this).closest('tr');
            //     var row = table.DataTable().row(tr);

            //     $(this).parents('tbody').find('.shown').removeClass('shown');
            //     $(this).parents('tbody').find('.row-details').remove();

            //     row.child(_format(row.data())).show();
            //     tr.addClass('shown');
            //     tr.next().addClass('row-details');
            // });

            //Date Pickers
            $('#daterangepicker').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                format: 'MM/DD/YYYY h:mm A'
            }, function (start, end, label) {
                console.log(start.toISOString(), end.toISOString(), label);
            });
        });
    </script>

@endsection
