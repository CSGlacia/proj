@extends('layouts.app')

@section('content')
<div class="container">
    <h1><b>NOT COMPLETE YET</b></h1>
    <div class="col-sm-12 col-md-12 col-lg-12">
    @if(count($bookings) > 0)    
        @foreach($bookings as $b)
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div>
                                <div>Property Name: {{$b->property_title}}</div>
                                <div>Address: {{$b->property_address}}</div>
                                <div>Suburb: {{$b->property_suburb}}</div>
                                <div>Persons: {{$b->booking_persons}}</div>
                            </div>
                            <span>Start Date: {{$b->booking_startDate}}</span>
                            <span>End Date: {{$b->booking_endDate}}</span>
                            <a class="btn btn-primary" name="review_property" href="/review_property?prop_id={{$b->property_id}}&booking_id={{$b->booking_id}}"> Review property</a>
                        </div>
                        <hr>
                    </div>
        @endforeach
    @else
        <div>You have no past bookings to review</div>
    @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

});
</script>
@endsection
