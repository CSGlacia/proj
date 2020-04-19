@extends('layouts.app')

@section('content')
<div class="container">
    <h2><b>Booking</b></h2>
    <hr>
    <div class="row">
        <div class="col-sm-6 col-md-6 col-lg-6">
            <h3><b>Property Details</b></h3>
            <div><i class="fas fa-home"></i>&nbsp;<a href="/view_property/{{$b->property_id}}">{{$b->property_title}}</a></div>
            <div>{{$b->property_address}}</div>
            <span><i class="fas fa-bed"></i> {{$b->property_beds}}</span>&nbsp;<span><i class="fas fa-bath"></i> {{$b->property_baths}}</span>&nbsp;<span><i class="fas fa-car"></i> {{$b->property_cars}}</span>
            <div>Description: {{$b->property_desc}}</div>
            <div>Owner: <a href="/user_profile/{{$b->id}}">{{$b->name}}</a></div>
            @if($b->scores != -1)
            <div ><i class="fas fa-star @if($b->scores > 2.5) gold-star @endif"></i>{{$b->scores}}</div>
            @endif
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
            <h3><b>Booking Details</b></h3>
            <div>Start Date: {{$b->booking_startDate}}</div>
            <div>End Date: {{$b->booking_endDate}}</div>
            <div>Persons Booked: {{$b->booking_persons}}</div>
            <div>Status: {{$status}}</div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
   
});
</script>
@endsection
