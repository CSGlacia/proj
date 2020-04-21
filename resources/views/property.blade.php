@extends('layouts.app')

@section('style')
<style>
ul {
    list-style: none outside none;
    padding-left: 0;
    margin-bottom:0;
}
li {
    display: block;
    float: left;
    margin-right: 6px;
    cursor:pointer;
}
img {
    display: block;
    height: auto;
    max-width: 100%;
}
</style>
@endsection

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
                    <div>
                        @if($avg_score > 2.5)
                            <i class="fas fa-star gold-star"></i>&nbsp;{{$avg_score}} ({{$p->num_ratings}} Review(s))
                        @elseif($avg_score == 0)
                            <i class="fas fa-star"></i>&nbsp;No reviews
                        @else
                            <i class="fas fa-star"></i>&nbsp;{{$avg_score}} ({{$p->num_ratings}} Review(s))
                        @endif
                    </div>
                </div>
            </div>
        </div>

    <!-- IMAGE START -->
        @if(count($images) > 0)
        <div class="row card">
            <div class="gallery"  style="width:800px; margin:auto; margin-top:20px; margin-bottom:20px;">
                <ul id="lightSlider">
                @foreach ($images as $image)
                    <li data-thumb='{{"https://turtle-database.s3-ap-southeast-2.amazonaws.com/".$image->property_image_name}}'>
                        <img class="gallery_image" src='{{"https://turtle-database.s3-ap-southeast-2.amazonaws.com/".$image->property_image_name}}' />
                    </li>
                @endforeach
                </ul>
            </div>
        </div>

        <div id="img_modal" class="modal">

          <span class="close" id="modal_close">&times;</span>
          <img class="modal-content" id="modal_img" style="width:100%;">
        </div>
        @endif

    <!-- IMAGE END -->

        @auth
        <div id="user_logged" data-logged="1" hidden></div>
        @else
        <div id="user_logged" data-logged="0" hidden></div>
        @endif

        <div class="row card" align="center">
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
                        <a id="book_submit" style="color:white" class="btn btn-primary">Book</a>
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
    <div class="col-sm-4 col-md-4 col-lg-4 pull-right" align="center">
        <div class="row card">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body">
                <div class="card-title" style="text-align:center;">
                    <h2>Bookings Awaiting Approval:</h2>
                </div>
                <hr>
                <div class="card-text">
                @if(count($bookings) > 0)
                    @foreach($bookings as $b)
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-12">
                                        <div>
                                            <div>Guest: <a href="/user_profile/{{$b->id}}">{{$b->name}}</a>
                                                @if($b->scores > 2.5)
                                                    <i class="fas fa-star gold-star"></i>&nbsp;{{$b->scores}}
                                                @elseif($b->scores == 0)
                                                    <i class="fas fa-star"></i>&nbsp;No reviews yet
                                                @else
                                                    <i class="fas fa-star"></i>&nbsp;{{$b->scores}}
                                                @endif
                                            </div>
                                            <div>Persons: {{$b->booking_persons}}</div>
                                            <span>Start Date: {{$b->booking_startDate}}</span>
                                            <div><span>End Date: {{$b->booking_endDate}}</span></div>
                                            <div style="margin-top:5px;"><a class="btn btn-primary" name="view_booking" href="/view_booking/{{$b->booking_id}}" data-id="{{$b->booking_id}}"> View Booking</a></div>
                                            <div style="margin-top:5px;"><span class="btn btn-success" name="approve_booking" data-id="{{$b->booking_id}}">Approve Booking</span>&nbsp;<span class="btn btn-warning" name="deny_booking" data-id="{{$b->booking_id}}">Deny Booking</span></div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                    @endforeach
                @else
                    <div>You have no bookings to approve</div>
                @endif
                </div>
            </div>
        </div>
        <div class="row card">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body">
                <div class="card-title" style="text-align:center;">
                    <h2>Upcoming Approved Bookings:</h2>
                </div>
                <hr>
                <div class="card-text">
                @if(count($abookings) > 0)
                    @foreach($abookings as $b)
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
                                            <div><a class="btn btn-primary" name="view_booking" href="/view_booking/{{$b->booking_id}}" data-id="{{$b->booking_id}}"> View Booking</a></div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                    @endforeach
                @else
                    <div>You have no upcoming approved bookings</div>
                @endif
                </div>
            </div>
        </div>
        <div class="row card">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body">
                <div class="card-title" style="text-align:center;">
                    <h2>Past Approved Bookings:</h2>
                </div>
                <hr>
                <div class="card-text">
                @if(count($pa_bookings) > 0)
                    @foreach($pa_bookings as $b)
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
                                            <div><a class="btn btn-primary" name="view_booking" href="/view_booking/{{$b->booking_id}}" data-id="{{$b->booking_id}}"> View Booking</a></div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                    @endforeach
                @else
                    <div>You have no past approved bookings</div>
                @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($page_owner == true)
    <div class="col-sm-4 col-md-4 col-lg-4 pull-right">
        <div class="row card">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body">
                <div class="card-title" style="text-align:center;">
                    <h2>Property Statistics:</h2>
                </div>
                <hr>
                Total page visits: {{$page_count}} <br>
                Average age: {{$avg_age}} <br>
                Average # tennants: {{$avg_persons}} <br>
                
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

    var modal = $('#img_modal')

    $(document).on('click', '[name="approve_booking"]', function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: '/approve_booking/'+id,
            method: 'GET',
            dataType: 'JSON',
            success: function(html) {
                if(html['status'] == "success") {
                    let timerInterval
                            Swal.fire({
                            title: 'Booking approved successfully',
                            html: 'You will be redirected in <b></b> seconds.',
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
                                location.reload();
                            }
                            }).then((result) => {
                                location.reload();
                            })
                } else if(html['status'] == 'overlapping_bookings') {
                    let timerInterval
                            Swal.fire({
                            title: 'An approved booking overlaps with this booking. This booking has been denied',
                            html: 'You will be redirected in <b></b> seconds.',
                            timer: 3000,
                            timerProgressBar: true,
                            type: "warning",
                            onBeforeOpen: () => {
                                Swal.showLoading()
                                timerInterval = setInterval(() => {
                                    swal.getContent().querySelector('b')
                                    .textContent = Math.ceil(swal.getTimerLeft() / 1000)
                                }, 100)
                            },
                            onClose: () => {
                                location.reload();
                            }
                            }).then((result) => {
                                location.reload();
                            })
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

    $(document).on('click', '[name="deny_booking"]', function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: '/deny_booking/'+id,
            method: 'GET',
            dataType: 'JSON',
            success: function(html) {
                console.log(html);
                if(html['status'] == "success") {
                    swal({
                        title:"Success!",
                        text: "Booking denied successfully",
                        type:"success",
                    });
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
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


    $(document).on('click', '#modal_close', function(e) {
        e.preventDefault();
        modal.hide();
    });

    $(document).on('click', '.gallery_image', function(e) {
        var img = $(this).attr('src');
        $('#modal_img').attr('src', img);
        modal.show();
        modal.css('display:block;')
    });

    $('#lightSlider').lightSlider({
        gallery: true,
        item: 1,
        loop: true,

    });

    $('#tags').select2({
        theme: "bootstrap"
    });

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

            if(epoch < {{time()}}) {
                return false;
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


            if(epoch < {{time()}}) {
                return false;
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
                        let timerInterval
                        Swal.fire({
                        title: 'Booking created successfully',
                        html: 'You will be redirected to your booking in <b></b> seconds.',
                        timer: 5000,
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
                            window.location.href = "/view_booking/"+html['id'];
                        }
                        }).then((result) => {
                            window.location.href = "/view_booking/"+html['id'];
                        })
                    } else if(html['status'] == 'bad_input') {
                        Swal.fire("Warning", "Please double check all fields are filled!", "warning");
                    } else if (html['status'] == 'time_booked'){
                        Swal.fire("Warning", "This booking date has already been taken!", "warning");
                    } else if (html['status'] == 'no_listings') {
                        Swal.fire("Warning", "The property is not available for booking during these dates", "warning");
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
                        let timerInterval
                        Swal.fire({
                        title: 'Adding this property to your wishlist',
                        html: 'You will be redirected in <b></b> seconds.',
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
                            location.reload();
                        }
                        }).then((result) => {
                            location.reload();
                        })
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
