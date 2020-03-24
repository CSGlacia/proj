@extends('layouts.app')

@section('content')
<div class="container">
    <span class="label bg-primary">Name: {{$user->name}}</span>
    <span class="label bg-warning">Email: {{$user->email}}</span>

    @if($page_owner)
    <div class="row">
        <b><h2>Bookings:</h2></b>
        <div class="col-sm-12 col-md-12 col-lg-12">
            @foreach($bookings as $b) 
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div>
                            <div>Property Name: {{$b->property_title}}</div>
                            <div>Address: {{$b->property_address}}</div>
                            <div>Suburb: {{$b->property_suburb}}</div>
                            <div>Persons: {{$b->persons}}</div>
                        </div>
                        <span>Start Date: {{$b->startDate}}</span>
                        <span>End Date: {{$b->endDate}}</span>
                        <a class="btn btn-primary" name="view_booking" data-id="{{$b->id}}"> View booking</a>
                    </div>
                    <hr>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    <div class="row">
        <b><h2>Properties:</h2></b>
        <hr>
        <div class="col-sm-12 col-md-12 col-lg-12">
          @foreach ($properties as $p)
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <h3><b> {{ $p->property_title }}</b></h3>
                    <div>Address: {{ $p->property_address }}</div>
                    <div>Suburb: {{ $p->property_suburb }}, {{ $p->property_postcode}} </div>
                    <span class="label bg-primary"> Beds: {{ $p->property_beds }} </span>
                    <span class="label bg-warning"> Bathrooms: {{ $p->property_baths }} </span>
                    <span class="label bg-danger"> Cars: {{ $p->property_cars }} </span>
                    <div> {{ $p->property_desc }}  </div>
                    <a class="btn btn-primary" name="view_property" href="/view_property/{{$p->property_id}}"> View property </a>
                </div>
            </div>
            <hr>
          @endforeach
        </div>
    </div>
    <div class="row">
        <b><h2>User Reviews</h2></b>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

});
</script>
@endsection
