@extends('admin.layouts.master')
@section('title', 'Vendor')
@section('page-css')
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card card-default px-5">
            <div class="card-header">
                <div class="card-title" style="width: 100%">
                    <div class="d-flex justify-content-between">
                        <h5><strong>{{ $vendor->name }}</strong></h5>
                        <h5>Vendor Details</h5>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-default" style="min-height: 18rem">
                        <div class="card-body text-center">
                            <img src="http://via.placeholder.com/450x120.png?text=Vendor+Logo" style="width: 100%" alt="logo">
                            <h4>{{ $vendor->street_name }}</h4>
                            <h4>{{ $vendor->city }}</h4>
                            <h4>{{ $vendor->state }}</h4>
                            <h4>{{ $vendor->zip_code }}</h4>
                            <p><a href="mailto:{{ $vendor->email }}">{{ $vendor->email }}</a></p>
                            <h4>{{ $vendor->hst }}</h4>
                            @if($vendor->store_no)
                                <p>{{ $vendor->store_no }}</p>
                            @endif
                            @if($vendor->tax_no)
                                <p>{{ $vendor->tax_no }}</p>
                            @endif
                            <button class="btn" style="margin-top: 1rem; background: #6d5cae; text-transform: uppercase; color: #fff; width: 100%">Add to Favourites</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div id="map" style="min-height: 18rem"></div>
                </div>  
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card card-default" style="text-align: center; min-height: 350px">
                        <h5 class="card-title" style="font-weight: bold; text-transform: uppercase">Quick Report</h5>
                        <hr style="margin: 0 25px;" />
                        <div class="card-body text-center">
                            <div style="display: flex; justify-content: center">
                                <select name="date" id="date" class="form-control" style="width: 30%">
                                    <option value="today">Today</option>
                                    <option value="this_week">This Week</option>
                                </select>
                            </div>
                            <hr />
                            <div style="width: 100%; display: flex; justify-content: center">
                                <div style="width: 50%;">
                                    <div><strong style="text-transform: uppercase">Total Spent:</strong>&nbsp;&nbsp;&nbsp;&nbsp;$250.00</div>
                                    <hr />
                                    <div><strong style="text-transform: uppercase">Transactions:</strong>&nbsp;&nbsp;&nbsp;&nbsp;11</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <img src="http://via.placeholder.com/750x250.png?text=Ad+Image" style="width: 100%" alt="Image">
                </div>  
            </div>
        </div>
    </div>
@endsection
@section('page-js')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_API') }}&callback" async></script>
    <script>
        let geoLocation;
        fetch(
        'https://maps.googleapis.com/maps/api/geocode/json?address={{ $vendor->address }}&key={{ env('GOOGLE_MAP_API') }}'
        ).then(async (res) => {
        try {
            const payload = await res.json();
            geoLocation = payload.results[0].geometry.location;
            const map = new google.maps.Map(document.getElementById('map'), {
                center: geoLocation,
                zoom: 15,
            });
            new google.maps.Marker({ position: geoLocation, map, title: '{{ $vendor->name }}'});
        } catch (error) {
                console.log(error);
            }
        });
    </script>
@endsection