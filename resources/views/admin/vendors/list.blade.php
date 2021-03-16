@extends('admin.layouts.master')

@section('title', 'Vendors List')

@section('page-css')

@endsection

@section('content')

    <!-- START CONTAINER FLUID -->
    <div class=" container-fluid   container-fixed-lg">
        <!-- START card -->
        <div class="card card-default">
            <div class="card-header separator">
                <div class="card-title">
                    <h5><strong>Vendors list</strong></h5>
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
                <div class="">
                    <table class="table table-hover table-condensed table-responsive-block table-responsive"
                           id="tableVendors">
                        <thead>
                        <tr>
                            <!-- NOTE * : Inline Style Width For Table Cell is Required as it may differ from user to user
                            Comman Practice Followed
                            -->
                            <th style="width:10%;">ID</th>
                            <th style="width: 10%;">Name</th>
                            <th style="width: 10%;">Email</th>
                            <th style="width:10%;">Address</th>
                            <th style="width: 10%;">Store No.</th>
                            <th style="width: 10%;">Tax No.</th>
                            {{--<th style="width: 20%;">Actions</th>--}}
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

            var table = $('#tableVendors');
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
                    "url": "{{ route('vendors.datatable') }}",
                    "type": "POST",
                    'data': function(data){
                        // data.order_no = $('#filter_order_no').val();
                    }
                },
                "order": [[ 0, "asc" ]],
                "columns": [
                    // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'address', name: 'address'},
                    {data: 'store_no', name: 'store_no'},
                    {data: 'tax_no', name: 'tax_no'},
                    // {data: 'actions', name: 'actions', orderable: false, searchable: false},
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
            // $('#tableVendors tbody').on('click', 'tr', function () {
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
