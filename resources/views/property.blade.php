@extends('layouts.app')

@section('content')
<div class="row" style="width:100%;">
    <div class="col-sm-8 col-md-8 col-lg-8">
        <div class="row card">
            <div class="card-body">
                <div class="card-title" style="text-align:center;">
                    <h2>{{$p->property_title}}</h2>
                </div>
                <hr>
                <div class="card-text">
                    <div style="margin:5px;">
                        <span><i class="fas fa-bed"></i>&nbsp;{{ $p->property_beds }} </span>
                        <span><i class="fas fa-toilet"></i>&nbsp;{{ $p->property_baths }} </span>
                        <span><i class="fas fa-car"></i>&nbsp;{{ $p->property_cars }} </span>
                    </div>
                    <div class="gallery">
                        @foreach ($images as $image)
                        <div class="">
                            <img src={{"https://turtle-database.s3-ap-southeast-2.amazonaws.com/".$image->property_image_name}}>
                        </div>
                        @endforeach
                    </div>

                    <div>{{$p->property_address}}, {{ $p->property_suburb }}, {{ $p->property_postcode}} </div>
                    <div style="margin:5px;"> {{ $p->property_desc }}  </div>
                </div>
            </div>
        </div>

        <div class="row card">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body">   
                <div class="card-title" style="text-align:center;">     
                    <h2>Current Availabilities</h2>
                </div>
            </div>
        </div>

            @auth
            <div id="user_logged" data-logged="1" hidden></div>
            @else
            <div id="user_logged" data-logged="0" hidden></div>
            @endif

        <div class="row card">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body">
                <div class="card-title" style="text-align:center;">
                    <h3>Make a Booking</h3>
                </div>
            <hr>
            <div id="listing_form" class="form-group">
                <div class="row">
                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <span>Start Date:&nbsp;</span>
                        <input id="startDate" class="form-control" type="date" placeholder="(dateTime)" required>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <span>End Date:&nbsp;</span>
                        <input id="endDate" class="form-control" type="date" placeholder="(dateTime)" required>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <span>Number of People:&nbsp;</span>
                        <input id="persons" class="form-control" type="number" placeholder="(int)" required>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <a id="book_submit" class="btn btn-primary">Book</a>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4 col-md-4 col-lg-4 pull-right">  
        <div class="row card">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body">
                <div class="card-title" style="text-align:center;">
                    <h2>Location</h2>
                </div>
                <hr>
                <!--The div element for the map -->
                <div id="map" style="height: 300px; width: 100%;"></div>
            </div>
        </div>
    </div>

    <div class="col-sm-4 col-md-4 col-lg-4 pull-right">  
        <div class="row card">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body">
                <div class="card-title" style="text-align:center;">
                    <h2>Reviews:</h2>
                </div>
                <hr>
                <div class="card-text">
                @if(count($reviews) > 0)
                    @foreach($reviews as $r)
                    <div class="row">
                        <div class="col-sm-6 col-md-6 col-lg-6">
                            <div>{{$r->prs_review}}</div>
                            <div>
                                <i class="fas fa-star @if($r->prs_score >= 1) gold-star @endif"></i> 
                                <i class="fas fa-star @if($r->prs_score >= 2) gold-star @endif"></i>
                                <i class="fas fa-star @if($r->prs_score >= 3) gold-star @endif"></i>
                                <i class="fas fa-star @if($r->prs_score >= 4) gold-star @endif"></i>
                                <i class="fas fa-star @if($r->prs_score >= 5) gold-star @endif"></i>
                            </div>

                            <div>Submitted by <a href="/user_profile/{{$r->id}}">{{$r->name}} </a>on {{$r->prs_submitted_at}}</div>
                        </div>
                    </div>
                    <hr>
                    @endforeach
                @else
                    <div>No reviews submitted for this property</div>
                @endif
                </div>
            </div>
        </div>
    </div>

    @if($page_owner)
    <div class="col-sm-4 col-md-4 col-lg-4 pull-right">  
        <div class="row card">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body">
                <div class="card-title" style="text-align:center;">
                    <h2>Current bookings:</h2>
                </div>
                <hr>
                <div class="card-text">
                @if(count($bookings) > 0)
                    @foreach($bookings as $b)
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-12">
                                        <div>
                                            <div>Guest: {{$b->name}}</div>
                                            <div>Persons: {{$b->booking_persons}}</div>
                                            <span>Start Date: {{$b->booking_startDate}}</span>
                                            <div><span>End Date: {{$b->booking_endDate}}</span></div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                    @endforeach
                @else 
                    <div>You have no tennants to review</div>
                @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBPDGVcOB-lSlE4tKgMpQwUbz_2d55B6xE&callback=initMap">
</script>

<script>
// Initialize and add the map
function initMap() {
  var prop_location = {lat: {{$p->property_lat}}, lng: {{$p->property_lng}}};

  var map = new google.maps.Map(
      document.getElementById('map'), {zoom: 13, center: prop_location,
        mapTypeControl: false,
        streetViewControl: false});

  var marker = new google.maps.Marker({position: prop_location, map: map});
  
}

$(document).ready(function() {
    $(document).on('click', '#book_submit', function(e) {
        var logged = $('#user_logged').data('logged');

        if(logged == 1) {
            e.preventDefault();

            var propertyID = {{$p->property_id}}
            var startDate = $('#startDate').val();
            var endDate = $('#endDate').val();
            var persons = $('#persons').val();

            $.ajax({
                url: '/create_booking',
                method: 'POST',
                dataType: 'JSON',
                data: 'propertyID='+propertyID+'&startDate='+startDate+'&endDate='+endDate+'&persons='+persons,
                success: function(html) {
                    var data = JSON.parse(html);
                    if(data['status'] == "success") {
                        alert("Success!");
                    } else if(data['status'] == 'bad_input') {
                        alert("Please double check all fields are filled!");
                    } else if (data['status'] == 'time_booked'){
                        alert("This booking date has already been taken!");
                    }
                    else {
                        alert("There was an error, please try again!");
                    }
                },
                error: function ( xhr, errorType, exception ) {
                    var errorMessage = exception || xhr.statusText;
                    alert("There was a connectivity problem. Please try again.");
                }
            });
        } else {
            alert("Please log in before making a booking");
        }
    });

});

</script>
@endsection
