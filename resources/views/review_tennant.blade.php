@extends('layouts.app')

@section('content')
<div class="container">
    <div><h2><b>Property Review</b></h2>
    <hr>
    <div class="row">
        <div class="col-sm-6 col-md-6 col-lg-6">
            <h3><b>Property Details</b></h3>
            <div>Property Name: {{$p->property_title}}</div>
            <div>Address: {{$p->property_address}}</div>
            <div>Suburb: {{$p->property_suburb}}</div>
            <div>Postcode: {{$p->property_postcode}}</div>
            <span>Beds: {{$p->property_beds}}</span>&nbsp;<span>Baths: {{$p->property_baths}}</span>&nbsp;<span>Cars: {{$p->property_cars}}</span>
            <div>Description: {{$p->property_desc}}</div>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
            <h3><b>Booking Details</b></h3>
            <div>Start Date: {{$b->booking_startDate}}</div>
            <div>End Date: {{$b->booking_endDate}}</div>
            <div>Persons Booked: {{$b->booking_persons}}</div>
            <div>Tennant: <a href="/user_profile/{{$b->id}}">{{$b->name}}</a></div>
        </div>

    </div>
    <hr>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <span>How would you rate your tennant's stay at the property?</span>
            <input id="rating" class="form-control" style="width:25%;" type="number" placeholder="1 - 5" min="1" max="5" required>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <span>Please add any additional comments about the tennant</span>
            <textarea id="review_desc" class="form-control" rows="5" placeholder="Additional comments" required></textarea>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <span class="btn btn-xs btn-primary" id="submit_review">Submit Review</span>
        </div>
    </div>    
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#rating').on('input', function(e) {
        var input = $(this).val();
        if(input < 1) {
            input = 1;
        }

        if(input > 5) {
            input = 5;
        }

        $('#rating').val(input);
    });

    $(document).on('click', 'submit_review', function(e){
        var score = $('#rating').val();
        var review = $('#review_desc').val();

        
    });
});
</script>
@endsection
