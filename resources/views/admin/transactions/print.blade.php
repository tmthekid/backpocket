<style>
    .transaction-print {
        display: none;
    }
    .invoice-footer {
        display: none;
    }
    @media print {
        @page {
            size: A4;
        }
        .transaction-page {
            display: none;
        }
        .breadcrumb {
            display: none;
        }
        .footer {
            display: none;
        }
        .transaction-print {
            margin-top: -4rem;
            display: block;
        }
        .address {
            margin-top: 25px;
            font-size: 1.3rem !important;
        }
        .total-top {
            margin-top: 50px;
            font-size: 3rem !important;
        }
        .transaction-info p {
            font-size: 1.3rem !important;
        }
        .transaction-table {
            margin-top: 1.5rem;
        }
        .transaction-table th {
            font-size: 1.1rem !important;
        }
        .transaction-table td {
            font-size: 1.1rem !important;
        }
        .invoice-footer {
            position: absolute;
            left: 0;
            bottom: 1%; 
            display: block;
            width: 100%;
        }
        .invoice-footer img {
            width: 20%;
        }
    }
    @media only screen and (max-width: 1200px){ 
        .extra {
            margin-top: .7rem;
            padding: 0 1.5rem;
        }
        .transaction-logo {
            margin-top: 5%;
        }
    }
    </style>
<div class="row transaction-table">
    <div class="transaction-print container">
        <div class="row">
            <div class="col-md-4">
                <img width="20%" src="{{ asset('admin/assets/img/vendor-logos/'. $transaction->vendor->logo .'.jpg') }}" alt="Logo">
                <p class="address">{{ $transaction->vendor->address }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 offset-md-8">
                <p class="text-right total-top">TOTAL: {{ $transaction->total }}</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 transaction-info">
                <p><strong>Date:</strong>&nbsp;{{ date("d/m/Y", strtotime($transaction->transaction_date)) }}</p>
                <p><strong>Time:</strong>&nbsp;{{ date("h:i A", strtotime($transaction->transaction_date)) }}</p>
                <p><strong>Order #</strong>&nbsp;{{ $transaction->order_no }}</p>
                <p><strong>Transaction #</strong>&nbsp;{{ $transaction->transaction_no }}</p>
            </div>
        </div>
        <hr class="my-5" />
        <table class="table">
            <thead>
                <tr>
                    <th class="text-uppercase text-left" scope="col" style="color: #626262 !important;">Item</th>
                    <th class="text-uppercase text-center" scope="col" style="color: #626262 !important;">Qty</th>
                    <th class="text-uppercase text-right" scope="col" style="color: #626262 !important;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->purchase as $purchase)
                    <tr>
                        <td style="border-left: 1px solid rgba(230, 230, 230, 0.7); border-right: 1px solid rgba(230, 230, 230, 0.7);">
                            <div class="font-weight-bold">
                                {{ $purchase->product->name }}
                            </div>
                            <div> 
                                @if($purchase->product->description)
                                    {!! $purchase->product->description !!}
                                @endif
                            </div>
                        </td>
                        <td style="border-right: 1px solid rgba(230, 230, 230, 0.7);">
                            <div style="display: flex; justify-content: center; align-items: center;">{{ $purchase->quantity }}</div>
                        </td>
                        <td style="border-right: 1px solid rgba(230, 230, 230, 0.7);">
                            <div style="display: flex; justify-content: flex-end; align-items: center;">{{ $purchase->price }}</div>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td style="border-left: 1px solid rgba(230, 230, 230, 0.7); border-right: 1px solid rgba(230, 230, 230, 0.7);">
                        <h5 class="text-uppercase font-weight-bold" style="font-size: 1.3rem;">Payment Information</h5>
                        <p class="text-uppercase mt-4" style="font-size: 1.1rem;"><span class="font-weight-bold">Method: </span>{{ $transaction->payment_method }}</p>
                        <p class="text-uppercase" style="font-size: 1.1rem;"><span class="font-weight-bold">Reference: </span>{{ $transaction->payment_ref }}</p>
                    </td>
                    <td colspan="2" style="border-right: 1px solid rgba(230, 230, 230, 0.7);">
                        <div>
                            <h5 class="text-uppercase text-right"><strong>Subtotal</strong>: {{ $transaction->sub_total }}</h5>
                            @foreach($extra_info as $info)
                                <h5 class="text-uppercase text-right"><strong>{{ $info['label'] }}</strong>: {{ $info['value'] }}</h5>
                            @endforeach
                            @if($transaction->tax_amount)
                                <h5 class="text-uppercase text-right"><strong>Taxes</strong>: {{ $transaction->tax_amount  }}</h5>
                            @endif
                            <hr>
                            <h5 class="text-right"><strong>Total: {{ $transaction->total }}</strong></h5>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="invoice-footer">
        <div class="d-flex align-items-center justify-content-between"> 
            <img src="{{ asset('admin/assets/img/footer.jpg') }}" alt="Logo">
            <div style="font-size: 1.1rem">BackPocket Inc.</div>
        </div>
        <hr/>
        <div class="d-flex align-items-center justify-content-between"> 
            <div style="font-size: 1.1rem">27 Evans Avenue, Toronto, Ontario, Canada M8Z 1K2</div>
            <div style="font-size: 1.1rem">info@backpocket.ca</div>
        </div>
    </div>
</div>