@extends('layouts.app')

@section('content')
<div class="container" style="background:rgba(240,255,248,0.6)">
    <div><h2><b>Edit Property Review</b></h2>
    <hr>
    <div class="row">
        <div class="col-sm-6 col-md-6 col-lg-6">
            <h3><b>Property Details</b></h3>
            <div><i class="fas fa-home"></i>&nbsp;<a href="/view_property/{{$review->property_id}}">{{$review->property_title}}</a></div>
            <div>{{$review->property_address}}</div>
            <span><i class="fas fa-bed"></i> {{$review->property_beds}}</span>&nbsp;<span><i class="fas fa-bath"></i> {{$review->property_baths}}</span>&nbsp;<span><i class="fas fa-car"></i> {{$review->property_cars}}</span>
            <div>Description: {{$review->property_desc}}</div>
            <div><i class="fas fa-user"></i>&nbsp;<a href="/user_profile/{{$review->id}}">{{$review->name}}</a></div>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
            <h3><b>Booking Details</b></h3>
            <div>Start Date: {{$review->booking_startDate}}</div>
            <div>End Date: {{$review->booking_endDate}}</div>
            <div>Persons Booked: {{$review->booking_persons}}</div>
        </div>

    </div>
    <hr>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <div>How would you rate your stay at the property?</div>
            <span>
                <span name="score-star"><i class="fas fa-star @if($review->prs_score >= 1) gold-star @endif" id="s1" data-num="1"></i></span>
                <span name="score-star"><i class="fas fa-star @if($review->prs_score >= 2) gold-star @endif" id="s2" data-num="2"></i></span>
                <span name="score-star"><i class="fas fa-star @if($review->prs_score >= 3) gold-star @endif" id="s3" data-num="3"></i></span>
                <span name="score-star"><i class="fas fa-star @if($review->prs_score >= 4) gold-star @endif" id="s4" data-num="4"></i></span>
                <span name="score-star"><i class="fas fa-star @if($review->prs_score >= 5) gold-star @endif" id="s5" data-num="5"></i></span>
            </span>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <span>Please add any additional comments about your property or your stay</span>
            <textarea id="review_desc" class="form-control" rows="5" placeholder="Additional comments" required>{!! $review->prs_review !!}</textarea>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12" align="center">
            <span class="btn btn-xs btn-primary" style="text-align:center ;color:white;width:20vw;height:6vh;margin-top:10px; margin-bottom:20px" id="submit_review">Save Review</span>
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

    var star_score = {{$review->prs_score}}

    $(document).on('click', '#submit_review', function(e){
        var score = star_score
        var review = $('#review_desc').val();

        $.ajax({
            url: '/update_property_review',
            method: 'POST',
            data: 'score='+score+'&review='+review+'&review_id='+{{$review->prs_id}},
            success: function(html) {
                html = JSON.parse(html);
                if(html['status'] == "success") {
                    let timerInterval
                    Swal.fire({
                    title: 'Review Edited Successfully',
                    html: 'You will be redirected to your reviews in <b></b> seconds.',
                    timer: 3000,
                    timerProgressBar: true,
                    type: "success",
                    onBeforeOpen: () => {
                        Swal.showLoading()
                        timerInterval = setInterval(() => {
                            swal.getContent().querySelector('b')
                            .textContent = Math.ceil(swal.getTimerLeft() / 1000)
                        }, 100)
                    },
                    onClose: () => {
                        window.location.href = "/property_reviews";
                    }
                    }).then((result) => {
                        window.location.href = "/property_reviews";
                    })
                } else if(html['status'] == 'bad_input') {
                    Swal.fire("Warning", "Please double check all fields are filled!", "warning");
                } else {
                    Swal.fire("Error", "There was an error, please try again!", "error");
                }
            },
            error: function ( xhr, errorType, exception ) {
                var errorMessage = exception || xhr.statusText;
                Swal.fire("Error", "There was a connectivity problem. Please try again.", "error");
            }
        });

    });
});
</script>
@endsection
