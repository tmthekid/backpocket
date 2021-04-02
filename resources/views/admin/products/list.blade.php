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
                    <h5><strong>Products</strong></h5>
                </div>
            </div>
            <div class="card-body p-t-20">
                <div class="row justify-content-left">
                    <div class="col-md-4">
                        <div class="form-group" style="display: inline-block">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Vendor</label>
                                </div>
                                <div class="col-md-7">
                                    <select id="vendor_filter" class="form-control">
                                        <option value="">Select Vendor</option>
                                        @foreach($vendors as $vendor)
                                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 form-inline">
                        <div class="form-group">
                            <label class="control-label mr-4">Product Name</label>
                            <input class="form-control" type="text" id="product_name">
                        </div>
                    </div>
                </div>
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
                            <th style="width: 10%;">Created</th>
                            {{--<th style="width: 20%;">Actions</th>--}}
                        </tr>
                        </thead>
                        <tbody>
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
            $.fn.dataTable.ext.errMode = 'none';
            var product_datatable = table.DataTable({
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
                        data.product_name = $('#product_name').val();
                        data.from = $('#from').val();
                        data.to = $('#to').val();
                        data.date_option = $('#date_options').val();
                        data.year_to_date = $('#year_to_date').val();
                    }
                },
                "order": [[ 0, "asc" ]],
                "columns": [
                    // {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'id', name: 'id'},
                    {data: 'vendor', name: 'vendor', fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html("<a style='color: #0090d9' href='/admin/vendors/"+oData.vendor_id+"'>"+oData.vendor+"</a>");
                    }},
                    {data: 'name', name: 'name'},
                    {data: 'sku', name: 'sku'},
                    {data: 'price', name: 'price'},
                    {data: 'created', name: 'created'},
                    // {data: 'actions', name: 'actions', orderable: false, searchable: false},
                ]
            });

            $("#vendor_filter").select2();

            $('#product_name').keyup( function() {
                product_datatable.draw();
            });

            $(document).on('change', '#vendor_filter', function () {
                product_datatable.draw();
            });

            $('#from').change( function() {
                product_datatable.draw();
            });
            $('#to').change( function() {
                product_datatable.draw();
            });
            $('#date_options').change( function() {
                product_datatable.draw();
            });
            $('#year_to_date').change( function() {
                product_datatable.draw();
            });

            $('#tableProducts thead tr').clone(true).appendTo('#tableProducts thead');
            $('#tableProducts thead tr:eq(1) th').each( function (i) {
                $(this).removeClass('sorting');
                var title = $(this).text();
                $(this).html('<input type="text" class="form-control" placeholder="Search '+title+'" />');
                $('input', this).on('keyup change click', function(e) {
                    e.stopPropagation();
                    if (product_datatable.column(i).search() !== this.value) {
                        product_datatable
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
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
