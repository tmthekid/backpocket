@extends('admin.layouts.master')

@section('title', 'Products List')

@section('page-css')
    <style>
        .select2-container{ width: 300px !important; }
    </style>
@endsection

@section('content')

    <!-- START CONTAINER FLUID -->
    <div class=" container-fluid   container-fixed-lg">
        <!-- START card -->
        <div class="card card-default">
            <div class="card-header separator">
                <div class="card-title">
                    <h5><strong>Products list</strong></h5>
                </div>
            </div>
            <div class="card-body p-t-20">
                <div class="row justify-content-left">
                    <div class="col-md-12">
                        <div class="form-group" style="display: inline-block">

                            <div class="row">
                                <div class="col-md-3">
                                    <label>Vendor</label>
                                </div>
                                <div class="col-md-7">
                                    <select id="vendor_filter" class="form-control">
                                        <option value="">Select vendir</option>
                                        @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{--<hr>--}}
                <div class="">
                    <table class="table table-hover table-condensed table-responsive-block table-responsive"
                           id="tableProducts">
                        <thead>
                        <tr>
                            <!-- NOTE * : Inline Style Width For Table Cell is Required as it may differ from user to user
                            Comman Practice Followed
                            -->
                            <th style="width:10%;">ID</th>
                            <th style="width: 10%;">Vendor</th>
                            <th style="width: 10%;">Product Name</th>
                            <th style="width:10%;">SKU</th>
                            <th style="width: 10%;">Price</th>
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

            var table = $('#tableProducts');
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
                "iDisplayLength": 10,
                "method": "post",
                "ajax": {
                    "url": "{{ route('products.datatable') }}",
                    "type": "POST",
                    'data': function(data){
                        data.vendor_id = $('#vendor_filter').val();
                    }
                },
                "order": [[ 0, "asc" ]],
                "columns": [
                    // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'id', name: 'id'},
                    {data: 'vendor', name: 'vendor'},
                    {data: 'name', name: 'name'},
                    {data: 'sku', name: 'sku'},
                    {data: 'price', name: 'price'},
                    // {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ]
            });

            $("#vendor_filter").select2();

            $(document).on('change', '#vendor_filter', function () {
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
            // $('#tableProducts tbody').on('click', 'tr', function () {
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
