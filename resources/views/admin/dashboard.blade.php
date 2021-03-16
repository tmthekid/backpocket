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
                <div class="card" style="background: #6f42c1; height: 130px">
                    <div class="card-body">
                        <h5 class="card-title" style="color: #fff">Search Vendor</h5>
                        <form method="POST">
                            <input class="form-control" name="search" id="searchVendor">
                            {{csrf_field()}}
                        </form>
                        <div id="vendorFilterList"></div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-sm-12">
                test
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-12">
                @include('admin.components.datatable', ['id' => 'vendorsTable', 'header' => 'Recent Vendors', 'headers' => ['id', 'Name', 'Email', 'Address', 'Store No.', 'Tax No.']])
            </div>
        </div>
    </div>
@endsection