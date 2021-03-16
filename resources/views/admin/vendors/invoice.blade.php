@extends('admin.layouts.invoice')

@section('title', 'Transaction Details')

@section('page-css')

@endsection

@section('content')

     <!-- START CONTAINER FLUID -->

     <div class="invoice padding-50 sm-padding-10">
         <div>
             <div class="row">
                 <div class="col-md-4">
                     <img width="235" height="47" alt="" class="invoice-logo"
                          data-src-retina="{{ asset('admin/assets/img/vendor-logos/'. $transaction->vendor->name .'.png') }}"
                          data-src="{{ asset('admin/assets/img/vendor-logos/'. $transaction->vendor->name .'.png') }}"
                          src="{{ asset('admin/assets/img/vendor-logos/'. $transaction->vendor->name .'.png') }}">
                     <address class="m-t-10"  style="width: 340px;">
                         {{ $transaction->vendor->address }}
                     </address>
                 </div>
                 <div class="col-md-5"></div>
                 <div class="col-md-3">
                     <div class="sm-m-t-10">
                         <h2 class="font-montserrat all-caps text-right font-weight-bold">
                             Total: {{ $transaction->total }}
                         </h2>
                         <address class="m-t-40 ">
                             <br />
                             <strong>Date:</strong> {{ date("j F, Y", strtotime($transaction->transaction_date)) }}
                             <br />
                             <strong>Time:</strong> {{ date("h:i A", strtotime($transaction->transaction_date)) }}
                             <br />
                             <strong>Transaction # </strong> {{ $transaction->transaction_no }}
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
                         <td class="v-align-middle text-left">{{ $purchase->product->name }}</td>
                         <td class="v-align-middle text-center">1</td>
                         <td class="v-align-middle text-right">{{ $purchase->price }}</td>
                     </tr>
                 @endforeach
                 <tr>
                     <td class="v-align-middle"
                         style="padding: 1px!important; border-bottom: none;">
                         <div class="b-a b-grey p-t-10 p-b-40 p-l-5 p-r-5">
                             <h5 class="m-b-10 font-weight-bold ml-2">PAYMENT
                                 INFORMATION
                             </h5>

                             <address
                                 class="m-t-10 justify-content-center p-r-50 ml-2">
                                 <strong>METHOD:</strong> &nbsp;&nbsp;&nbsp; {{ $transaction->payment_method }}
                                 <br>
                                 <strong>REFERENCE:</strong>
                                 &nbsp;N/A
                             </address>
                         </div>

                     </td>
                     <td class="v-align-middle text-center" colspan="2"
                         style="border-bottom: none;">
                         <div
                             class="text-right justify-content-center align-items-end m-b-20 m-t-10">
                             <strong>SUBTOTAL:</strong>&nbsp;&nbsp; {{ $transaction->sub_total }}<br>
                             @if($transaction->discount != null)
                                 <strong>Discount:</strong>&nbsp;&nbsp; {{ $transaction->discount }}<br>
                             @endif
                             <strong>TAXES:</strong>&nbsp;&nbsp;&nbsp;&nbsp;
                             {{ $transaction->tax_amount }} <hr />
                             <strong style="font-size: 18px;">
                                 Total: {{ $transaction->total }}
                             </strong>
                         </div>


                     </td>
                 </tr>
                 </tbody>
             </table>
         </div>
         <br>
     </div>

@endsection

@section('page-js')

    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js" integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous"></script>--}}

@endsection
