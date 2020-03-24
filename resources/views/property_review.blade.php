@extends('layouts.app')

@section('content')
<div class="container">
    <h1><b>NOT COMPLETE YET</b></h1>
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
                        <a class="btn btn-primary" name="view_booking" href="/review_property/{{$b->property_id}}"> Review property</a>
                    </div>
                    <hr>
                </div>
    @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

});
</script>
@endsection
