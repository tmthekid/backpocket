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
                        <div class="card-body">
                            <h4>{{ $vendor->address }}</h4>
                            <p><a href="mailto:{{ $vendor->email }}">{{ $vendor->email }}</a></p>
                            @if($vendor->store_no)
                                <p>{{ $vendor->store_no }}</p>
                            @endif
                            @if($vendor->tax_no)
                                <p>{{ $vendor->tax_no }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div id="map" style="min-height: 18rem"></div>
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