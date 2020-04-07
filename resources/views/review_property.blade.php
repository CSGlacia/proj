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
            <div>Owner: <a href="/user_profile/{{$p->id}}">{{$p->name}}</a></div>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
            <h3><b>Booking Details</b></h3>
            <div>Start Date: {{$b->booking_startDate}}</div>
            <div>End Date: {{$b->booking_endDate}}</div>
            <div>Persons Booked: {{$b->booking_persons}}</div>
        </div>

    </div>
    <hr>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div>How would you rate your stay at the property?</div>
            <span>
                <span name="score-star"><i class="fas fa-star gold-star" id="s1" data-num="1"></i></span>
                <span name="score-star"><i class="fas fa-star" id="s2" data-num="2"></i></span>
                <span name="score-star"><i class="fas fa-star" id="s3" data-num="3"></i></span>
                <span name="score-star"><i class="fas fa-star" id="s4" data-num="4"></i></span>
                <span name="score-star"><i class="fas fa-star" id="s5" data-num="5"></i></span>
            </span>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <span>Please add any additional comments about your property or your stay</span>
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
    $(document).on('mouseover', '.fa-star', function() {
        num = $(this).data("num");

        if(num == 1) {
            $('#s1').addClass('gold-star-temp');
        }

        if(num == 2) {
            $('#s1').addClass('gold-star-temp');
            $('#s2').addClass('gold-star-temp');
        }

        if(num == 3) {
            $('#s1').addClass('gold-star-temp');
            $('#s2').addClass('gold-star-temp');
            $('#s3').addClass('gold-star-temp');
        }

        if(num == 4) {
            $('#s1').addClass('gold-star-temp');
            $('#s2').addClass('gold-star-temp');
            $('#s3').addClass('gold-star-temp');
            $('#s4').addClass('gold-star-temp');
        }

        if(num == 5) {
            $('#s1').addClass('gold-star-temp');
            $('#s2').addClass('gold-star-temp');
            $('#s3').addClass('gold-star-temp');
            $('#s4').addClass('gold-star-temp');
            $('#s5').addClass('gold-star-temp');
        }
    });

    $(document).on('mouseout', '.fa-star', function() {
        $('#s1').removeClass('gold-star-temp');
        $('#s2').removeClass('gold-star-temp');
        $('#s3').removeClass('gold-star-temp');
        $('#s4').removeClass('gold-star-temp');
        $('#s5').removeClass('gold-star-temp');

    });

    $(document).on('click', '[name="score-star"]', function() {
        num = $(this).find("i").data("num");

        $('#s1').removeClass('gold-star');
        $('#s2').removeClass('gold-star');
        $('#s3').removeClass('gold-star');
        $('#s4').removeClass('gold-star');
        $('#s5').removeClass('gold-star');

        if(num == 1) {
            $('#s1').addClass('gold-star');
        }

        if(num == 2) {
            $('#s1').addClass('gold-star');
            $('#s2').addClass('gold-star');
        }

        if(num == 3) {
            $('#s1').addClass('gold-star');
            $('#s2').addClass('gold-star');
            $('#s3').addClass('gold-star');
        }

        if(num == 4) {
            $('#s1').addClass('gold-star');
            $('#s2').addClass('gold-star');
            $('#s3').addClass('gold-star');
            $('#s4').addClass('gold-star');
        }

        if(num == 5) {
            $('#s1').addClass('gold-star');
            $('#s2').addClass('gold-star');
            $('#s3').addClass('gold-star');
            $('#s4').addClass('gold-star');
            $('#s5').addClass('gold-star');
        }
        star_score = num;
    });

    var star_score = 0

    $(document).on('click', '#submit_review', function(e){
        var score = star_score
        var review = $('#review_desc').val();

        $.ajax({
            url: '/create_property_review',
            method: 'POST',
            dataType: 'JSON',
            data: 'score='+score+'&review='+review+'&booking_id='+{{$b->booking_id}}+'&property_id='+{{$p->property_id}},
            success: function(html) {
                if(html['status'] == "success") {
                    alert("Review Submitted Successfully");
                } else if(html['status'] == 'bad_input') {
                    alert("Please double check all fields are filled!");
                } else {
                    alert("There was an error, please try again!");
                }
            },
            error: function ( xhr, errorType, exception ) {
                var errorMessage = exception || xhr.statusText;
                alert("There was a connectivity problem. Please try again.");
            }
        });

    });
});
</script>
@endsection
