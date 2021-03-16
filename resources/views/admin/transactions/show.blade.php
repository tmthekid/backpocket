@extends('admin.layouts.master')

@section('title', 'Transaction Details')

@section('page-css')

@endsection

@section('content')

     <!-- START CONTAINER FLUID -->
                <div class=" container-fluid">
                    <!-- START card -->
                    <div class="card card-default">
                        <div class="card-header separator">
                            <div class="card-title">
                                <h5><strong>Transaction Details</strong></h5>

                            </div>
                        </div>
                        <div class="card-body p-t-20">
                            <!-- <div class="container-fluid"> -->
                            <div class="row">
                                <div class="col-md-7">
                                    <div class="card card-default">
                                        <div class="invoice padding-50 sm-padding-10">
                                            <div>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        {{--<img width="235" height="47" alt="" class="invoice-logo"--}}
                                                        <img height="60" alt="" class="invoice-logo"
                                                            data-src-retina="{{ asset('admin/assets/img/vendor-logos/'. $transaction->vendor->logo .'.png') }}"
                                                            data-src="{{ asset('admin/assets/img/vendor-logos/'. $transaction->vendor->logo .'.png') }}"
                                                            src="{{ asset('admin/assets/img/vendor-logos/'. $transaction->vendor->logo .'.png') }}">
                                                        <address class="m-t-10 text-center">
                                                            {{ $transaction->vendor->address }}
                                                        </address>
                                                    </div>
                                                    <div class="col-md-4"></div>
                                                    <div class="col-md-4">
                                                        <div class="sm-m-t-20">
                                                            <h2 class="font-montserrat all-caps text-center font-weight-bold">
                                                                {{ $transaction->total }}
                                                            </h2>
                                                            <address class="m-t-10 text-center">
                                                                <!-- November 11, 2019 <br> -->
                                                                {{ date("j F, Y", strtotime($transaction->transaction_date)) }} <br />
                                                                {{ date("h:i A", strtotime($transaction->transaction_date)) }} <br>
                                                                Order # {{ $transaction->order_no }} <br />
                                                                Transaction # {{ $transaction->transaction_no }}
                                                            </address>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="table-responsive table-invoice">
                                                <table class="table m-t-10">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-left">ITEM</th>
                                                            <th class="text-center">QTY</th>
                                                            <th class="text-right">AMOUNT</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($transaction->purchase as $purchase)
                                                        <tr>
                                                            <td class="v-align-middle text-left">
                                                                <strong>{{ $purchase->product->name }}</strong>
                                                                @if($purchase->product->description)
                                                                    <br />
                                                                    {!! $purchase->product->description !!}
                                                                @endif
                                                            </td>
                                                            <td class="v-align-middle text-center">1</td>
                                                            <td class="v-align-middle text-right">{{ $purchase->price }}</td>
                                                        </tr>
                                                        @endforeach
                                                        @if($extra_info && $extra_info->where('type', 'desc')->count())
                                                        <tr>
                                                            <td class="v-align-middle text-center" colspan="3"
                                                                style="padding: 1px!important; border-bottom: none;">
                                                                <div class="b-a b-grey p-t-10 p-b-40 p-l-5 p-r-5">
                                                                    <h5 class="m-b-20 font-weight-bold">EXTRA INFORMATION</h5>
                                                                    <div class="row">
                                                                        @foreach($extra_info as $info)
                                                                            @if($info['type'] == 'desc')
                                                                                <div class="col-md-6 border p-2">
                                                                                    <strong>{{ $info['label'] }}</strong>
                                                                                </div>
                                                                                <div class="col-md-6 border p-2">
                                                                                    {!! $info['value'] !!}
                                                                                </div>
                                                                            @endif
                                                                        @endforeach

                                                                    </div>
                                                                </div>

                                                            </td>
                                                        </tr>
                                                        @endif
                                                        <tr>
                                                            <td class="v-align-middle text-center"
                                                                style="padding: 1px!important; border-bottom: none;">
                                                                <div class="b-a b-grey p-t-10 p-b-40 p-l-5 p-r-5">
                                                                    <h5 class="m-b-30 font-weight-bold">PAYMENT INFORMATION
                                                                    </h5>

                                                                    <address
                                                                        class="m-t-10 text-right justify-content-center p-r-50">
                                                                        <strong>METHOD:</strong> {{ $transaction->payment_method }}
                                                                        <br>
                                                                        <strong>REFERENCE:</strong> {{ $transaction->payment_ref }}
                                                                    </address>
                                                                </div>

                                                            </td>
                                                            <td class="v-align-middle text-center" colspan="2"
                                                                style="border-bottom: none;">
                                                                <div class="text-right justify-content-center align-items-end m-b-20 m-t-10">
                                                                    <strong>SUBTOTAL:</strong> {{ $transaction->sub_total }}<br>
                                                                    @if($extra_info && $extra_info->where('type', 'amount')->count())
                                                                        @foreach($extra_info as $info)
                                                                            @if($info['type'] == 'amount')
                                                                                <strong>{{ $info['label'] }}:</strong>
                                                                                {{ $info['value'] }} <br>
                                                                            @endif
                                                                        @endforeach
                                                                    @endif
                                                                    @if($transaction->vendor->name != 'Apple')
                                                                        <strong>TAXES:</strong>
                                                                        {{ $transaction->tax_amount }}
                                                                    @endif
                                                                </div>

                                                                <div
                                                                    class="text-right bg-master-darker col-sm-height padding-10 d-flex flex-column justify-content-center align-items-end">
                                                                    <h5
                                                                        class="font-montserrat all-caps small no-margin hint-text text-white bold">
                                                                        Total</h5>
                                                                    <h1 class="no-margin text-white">{{ $transaction->total }}</h1>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <br>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="card card-default">
                                        <div class="card-header separator">
                                            <div class="card-title">
                                                <div class="row justify-content-center">
                                                    <div class="col-md-4">
                                                        <button class="btn btn-primary btn-cons m-b-10 btn-block"
                                                                type="button" onclick="window.print()">
                                                            <i class="fa fa-print"></i> <span
                                                                class="bold">PRINT</span>
                                                        </button>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <button class="btn btn-success btn-cons m-b-10 btn-block"
                                                            type="button"><i class="fa fa-envelope"></i> <span
                                                                class="bold">EMAIL</span>
                                                        </button>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <a href="{{ route('transactions.mpdf', ['transaction' => $transaction->id]) }}" class="btn btn-info btn-cons m-b-10 btn-block p-l-10"
                                                            type="button"><i class="fa fa-download"></i> <span
                                                                class="bold">DOWNLOAD</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="font-weight-bold"><strong>Organize</strong></h5>
                                            <p class="m-b-20">We've made it easy for you to sort receipts and organize
                                                your finances.</p>
                                            <p class="m-b-30">Add them to `Envelopes` to categorize your expenses.
                                                Create Budgets to track your goal vs actual expenses</p>
                                            <form action="" id="form-env">
                                                <div class="input-group required">
                                                    <input type="text" class="form-control"
                                                        placeholder="Add to Envelope" required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text primary"
                                                            style="cursor: pointer;">ADD
                                                        </span>
                                                    </div>
                                                </div>

                                                <p class="small m-t-10">
                                                    <a href="manage-envelopes.html"><span>Go To Envelopes Manager</span>
                                                        <i
                                                            class="fa fs-12 fa-arrow-circle-o-right text-success m-l-10"></i></a>
                                                </p>
                                            </form>
                                            <br>
                                            <form action="" id="form-budget">
                                                <div class="input-group required">
                                                    <input type="text" class="form-control" placeholder="Add to Budget"
                                                        required>
                                                    <div class="input-group-append">
                                                        <span class="input-group-text primary"
                                                            style="cursor: pointer;">ADD
                                                        </span>
                                                    </div>
                                                </div>

                                                <p class="small m-t-10">
                                                    <a href="#!"><span>Go To Budget Manager</span>
                                                        <i
                                                            class="fa fs-12 fa-arrow-circle-o-right text-success m-l-10"></i></a>
                                                </p>
                                            </form>
                                            <br>
                                            <h5 class="font-weight-bold"><strong>Archive It!</strong></h5>
                                            <p class="m-b-20">Don't need receipt anymore? Put them away quickly with our
                                                one touch archive</p>
                                            <form action="" id="form-archive">
                                                <div class="input-group required">
                                                    <button type="button" class="btn btn-primary btn-block">SEND TO
                                                        ARCHIVES</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- </div> -->
                </div>

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
                    {data: 'order_no', name: 'order_no'},
                    {data: 'vendor_id', name: 'vendor_id'},
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
