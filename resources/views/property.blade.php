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
                        <span><i class="fas fa-bath"></i>&nbsp;{{ $p->property_baths }} </span>
                        <span><i class="fas fa-car"></i>&nbsp;{{ $p->property_cars }} </span>
                    </div>
                    <div class="gallery">
                        @foreach ($images as $image)
                        <div class="">
                            <img src={{"https://turtle-database.s3-ap-southeast-2.amazonaws.com/".$image->property_image_name}}>
                        </div>
                        @endforeach
                    </div>
                    <div>{{$p->property_address}}</div>
                    <div style="margin:5px;"> {{ $p->property_desc }}  </div>
                    <div>
                        @foreach($tags as $t)
                            <span class="badge badge-secondary">{{$t['text']}}</span>
                        @endforeach
                    </div>
                    <div class="float-right">
                        @if($page_owner == true)
                        <label id="delete_property" class="btn btn-primary">✖ Delete Property</label>
                        @endif
                        @auth
                        <label id="add_to_wishlist" class="btn btn-primary">★ Add to my Wishlist</label>
                        @endauth
                    </div>

                </div>
            </div>
        </div>

    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <h2><b>Current Availabilities</b></h2>
            <hr>
            {{-- @if(count($avail) > 0)
                @foreach ($avail as $a)
                    <h4> {{ $a->booking_startDate }} - {{$a->booking_endDate}} </h4>
                @endforeach
            @else
                <h4> Everything is available!</h4>
            @endif --}}
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
                        <input id="startDate" class="form-control" type="text"  required>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <span>End Date:&nbsp;</span>
                        <input id="endDate" class="form-control" type="text" required>
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
                            @if($r->prs_edited == 1)
                            <div>Edited on {{$r->prs_edited_at}}</div>
                            @endif
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

    @if($page_owner == true)
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
                                            <div>Guest: <a href="/user_profile/{{$b->id}}">{{$b->name}}</a> {{$b->scores}}
                                                @if($b->scores > 2.5)
                                                    <i class="fas fa-star gold-star"></i>
                                                @else
                                                    <i class="fas fa-star"></i>
                                                @endif
                                            </div>
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

    $('#tags').select2();

    var startDate = $('#startDate').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        beforeShowDay: function(date) {
            var listings = @json($cal_listings);
            var dateStr = (parseInt(date.getYear())+1900)+'-'+(parseInt(date.getMonth())+1)+'-'+date.getDate();
            var epoch = moment(dateStr).unix();
            //fix 6 hour time diff
            epoch = epoch + 36000;

            var bool = false;
            for(i = 0; i < listings.length; i++) {
                if(listings[i]['reccurring'] == 1) {
                    //for next year allow bookings
                    if((epoch >= listings[i]['start'] && epoch <= listings[i]['end']) || (epoch >= listings[i]['start']+31536000 && epoch <= listings[i]['end']+31536000))   {
                        bool = true;
                    }
                } else {
                    if(epoch >= listings[i]['start'] && epoch <= listings[i]['end']) {
                        bool = true;
                    }
                }
            }
            if(listings.length == 0) {
                bool = true;
            }
            var bookings = @json($cal_bookings);
            for(i = 0; i < bookings.length; i++) {
                if(epoch >= bookings[i]['start'] && epoch <= bookings[i]['end']) {
                    bool = false;
                }
            }
            return bool;
        },
    });
    var endDate = $('#endDate').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        beforeShowDay: function(date) {
            var listings = @json($cal_listings);
            var dateStr = (parseInt(date.getYear())+1900)+'-'+(parseInt(date.getMonth())+1)+'-'+date.getDate();
            var epoch = moment(dateStr).unix();
            var bool = false;
            //fix 6 hour time diff
            epoch = epoch + 36000;

            for(i = 0; i < listings.length; i++) {

                if(listings[i]['reccurring'] == 1) {
                    //for next year allow bookings
                    if((epoch >= listings[i]['start'] && epoch <= listings[i]['end']) || (epoch >= listings[i]['start']+31536000 && epoch <= listings[i]['end']+31536000))   {
                        bool = true;
                    }
                } else {
                    if(epoch >= listings[i]['start'] && epoch <= listings[i]['end']) {
                        bool = true;
                    }
                }
            }
            if(listings.length == 0) {
                bool = true;
            }
            var bookings = @json($cal_bookings);
            for(i = 0; i < bookings.length; i++) {
                if(epoch >= bookings[i]['start'] && epoch <= bookings[i]['end']) {
                    bool = false;
                }
            }
            return bool;
        }
    });

    $('#startDate').change(function() {
        var newStartDate = startDate.val();
        newStartDate = newStartDate.split('/');
        newStartDate = newStartDate[2]+'-'+newStartDate[1]+'-'+newStartDate[0];
        newStartDate = new Date(newStartDate);
        endDate.datepicker("setStartDate",  newStartDate);
    });

    $('#endDate').change(function() {
        var newEndDate = endDate.val();
        newEndDate = newEndDate.split('/');
        newEndDate = newEndDate[2]+'-'+newEndDate[1]+'-'+newEndDate[0];
        newEndDate = new Date(newEndDate);

        startDate.datepicker("setEndDate",  newEndDate);
    });

/*
    function test() {
        var epoch = moment('2020-05-29').unix();
        console.log(epoch);
        var listings = @json($cal_listings);
        console.log(listings);
        var bookings = @json($cal_bookings);
        console.log(bookings);
    }
    test();
    */
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
                    if(html['status'] == "success") {
                        Swal.fire("Success", "Booking created successfully", "success");
                    } else if(html['status'] == 'bad_input') {
                        Swal.fire("Warning", "Please double check all fields are filled!", "warning");
                    } else if (html['status'] == 'time_booked'){
                        Swal.fire("Warning", "This booking date has already been taken!", "warning");
                    }
                    else {
                        Swal.fire("Error", "There was an error, please try again!", "error");
                    }
                },
                error: function ( xhr, errorType, exception ) {
                    var errorMessage = exception || xhr.statusText;
                    Swal.fire("Error", "There was a connectivity problem. Please try again.", "error");
                }
            });
        } else {
            Swal.fire("Warning", "Please log in before making a booking", "warning");
        }
    });
});
</script>

<script>
$(document).ready(function() {
    $(document).on('click', '#delete_property', function(e) {
        var logged = $('#user_logged').data('logged');
        if(logged == 1) {
            e.preventDefault();
            var propertyID = {{$p->property_id}};

            $.ajax({
                url: '/delete_property',
                method: 'POST',
                dataType: 'JSON',
                data: 'propertyID='+propertyID,
                success: function(html) {
                    if(html['status'] == "success") {
                        swal({
                            title:"Success!",
                            text: "You have successfully deleted your property.",
                            type:"success",
                        }).then(function(){
                            window.location.href = "/";
                        });
                    } else {
                        Swal.fire("Error", "There was an error, please try again!", "error");
                    }
                },
                error: function ( xhr, errorType, exception ) {
                    var errorMessage = exception || xhr.statusText;
                    Swal.fire("Error", "There was a connectivity problem. Please try again.", "error");
                }
            });
        } else {
            Swal.fire("Error", "An error occurred!", "error");
        }
    });
});
</script>

<script>
$(document).ready(function() {
    $(document).on('click', '#add_to_wishlist', function(e) {
        var logged = $('#user_logged').data('logged');
        if(logged == 1) {
            e.preventDefault();
            var propertyID = {{$p->property_id}};
            var propertyTitle = "{{$p->property_title}}";
            var propertyAddress = "{{$p->property_address}}";

            $.ajax({
                url: '/add_to_wishlist',
                method: 'POST',
                dataType: 'JSON',
                data: 'propertyID='+propertyID+'&propertyTitle='+propertyTitle+'&propertyAddress='+propertyAddress,
                success: function(html) {
                    if(html['status'] == "success") {
                        Swal.fire("Success", "Added this listing to your wishlist!", "success");
                    } else if(html['status'] == "exists"){
                        Swal.fire("Error","You have already added this property to your wishlist","error");
                    }
                    else {
                        Swal.fire("Error", "There was an error, please try again!", "error");
                    }
                },
                error: function ( xhr, errorType, exception ) {
                    var errorMessage = exception || xhr.statusText;
                    Swal.fire("Error", "There was a connectivity problem. Please try again.", "error");
                }
            });
        } else {
            Swal.fire("Error", "An error occurred!", "error");
        }
    });
});
</script>
@endsection
