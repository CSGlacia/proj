@extends('layouts.app')

@section('content')
<div class="container"align="center">
    <div class="card" style="background:rgba(240,255,248,0.6)">
        <span class="display-4" style="text-transform:uppercase; font-family:'arial';"> {{$user->name}} </span>
        <div>
            <a class="label" >Email: {{$user->email}}</a>
            <div>
                <a class="label" >Guest rating: </a>
                <a>{{$guest_score}}</a>
                <a name="score-star"><i class="fas fa-star gold-star"></i></a>
            </div>
        </div>
    </div>
    @if($page_owner)
    <div class="container row">
        <div class="col-sm-6 col-md-6 col-lg-6" style="background:rgba(255,155,155,0.3)">
            <b><h2>Your Unapproved Bookings:</h2></b>
            <hr>
            <div class="col-sm-12 col-md-12 col-lg-12">
                @foreach($bookings as $b)
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 card-body card">
                            <div class="card-title">
                                <h3>{{ $b->property_title }}</h3>
                            </div>
                            <div>
                                <div>Address: {{$b->property_address}}</div>
                                <div>Persons: {{$b->booking_persons}}</div>
                            </div>
                            <span>Start Date: {{$b->booking_startDate}}</span>
                            <span>End Date: {{$b->booking_endDate}}</span>
                            <div>
                                <a class="btn btn-primary" name="view_booking" href="/view_booking/{{$b->booking_id}}" data-id="{{$b->booking_id}}"> View booking</a>
                                <a class="btn btn-danger" name="delete_booking" data-id="{{$b->booking_id}}" style="color:white"> Cancel booking</a>
                            </div>
                        </div>
                        <hr>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6" style="background:rgba(135,255,155,0.3)">
            <b><h2>Your Approved Bookings:</h2></b>
            <hr>
            <div class="col-sm-12 col-md-12 col-lg-12">
                @foreach($abookings as $b)
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 card-body card">
                            <div class="card-title">
                                <h3>{{ $b->property_title }}</h3>
                            </div>
                            <div>
                                <div>Address: {{$b->property_address}}</div>
                                <div>Persons: {{$b->booking_persons}}</div>
                            </div>
                            <span>Start Date: {{$b->booking_startDate}}</span>
                            <span>End Date: {{$b->booking_endDate}}</span>
                            <div>
                                <a class="btn btn-primary" name="view_booking" href="/view_booking/{{$b->booking_id}}" data-id="{{$b->booking_id}}"> View booking</a>
                                <a class="btn btn-danger" name="delete_booking" data-id="{{$b->booking_id}}"style="color:white"> Cancel booking</a>
                            </div>
                        </div>
                        <hr>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="container row">
        <div class="col-sm-6 col-md-6 col-lg-6" style="background:rgba(0,191,255,0.25)">
            <b><h2>Your Past Bookings:</h2></b>
            <hr>
            <div class="col-sm-12 col-md-12 col-lg-12">
                @foreach($pbookings as $b)
                    <div class="row card">
                        <div class="col-sm-12 col-md-12 col-lg-12 card-body">
                            <div class="card-title">
                                <h3>{{ $b->property_title }}</h3>
                            </div>
                            <div>
                                <div>Address: {{$b->property_address}}</div>
                                <div>Persons: {{$b->booking_persons}}</div>
                            </div>
                            <span>Start Date: {{$b->booking_startDate}}</span>
                            <span>End Date: {{$b->booking_endDate}}</span>
                            <div>
                                <button class="btn btn-primary" name="view_booking" href="/view_booking/{{$b->booking_id}}" data-id="{{$b->booking_id}}"> View booking</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6" style="background:rgba(0,91,155,0.8)">
            <b><h2>Your Denied Bookings:</h2></b>
            <hr>
            <div class="col-sm-12 col-md-12 col-lg-12">
                @foreach($dbookings as $b)
                    <div class="row card">
                        <div class="col-sm-12 col-md-12 col-lg-12 card-body">
                            <div class="card-title">
                                <h3>{{ $b->property_title }}</h3>
                            </div>
                            <div>
                                <div>Address: {{$b->property_address}}</div>
                                <div>Persons: {{$b->booking_persons}}</div>
                            </div>
                            <span>Start Date: {{$b->booking_startDate}}</span>
                            <span>End Date: {{$b->booking_endDate}}</span>
                            <div>
                                <button class="btn btn-primary" name="view_booking" href="/view_booking/{{$b->booking_id}}" data-id="{{$b->booking_id}}"> View booking</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="card" style="background:rgba(240,255,248,0.6)">
        <b><h2>Properties:</h2></b>
        <hr>
        <div class="col-sm-12 col-md-12 col-lg-12">
      @foreach ($properties as $p)
        <div class="row card item-card cursor-pointer" name="view_property" data-id="{{$p->property_id}}" style="margin:5px; border:none; width:50vw;">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body" align="left">

                @if($p->property_image_name[0])
                    <img class="float-right" height="160vh" src={{"https://turtle-database.s3-ap-southeast-2.amazonaws.com/".$p->property_image_name[0]}}>
                @endif
                <div class="card-title">
                <h3>{{ $p->property_title }}</h3>
                </div>
                <div class="card-text">
                <div style="margin:5px;">
                    <span><i class="fas fa-bed"></i>&nbsp;{{ $p->property_beds }} </span>
                    <span><i class="fas fa-bath"></i>&nbsp;{{ $p->property_baths }} </span>
                    <span><i class="fas fa-car"></i>&nbsp;{{ $p->property_cars }} </span>
                </div>
                <div>{{ $p->property_address }}</div>
                <div style="margin:5px;"> {{ $p->property_desc }}  </div>
                <div>
                    @foreach($p->tags as $t)
                        <span class="badge badge-secondary">{{$t}}</span>
                    @endforeach
                </div>
                    <div><i class="fas fa-star @if($p->scores > 2.5 && $p->scores != 'No Reviews Yet') gold-star @endif"></i>&nbsp;{{$p->scores}}@if($p->scores != "No Reviews Yet")({{$p->review_count}} Review(s))@endif</div>
                </div>
                <a class="btn btn-primary" name="view_property" href="/view_property/{{$p->property_id}}"> View property </a>
                <a class="btn btn-info" name="edit_property" href="/edit_property/{{$p->property_id}}" style="color:white"> Edit property </a>
            </div>
        </div>
      @endforeach
        </div>
    </div>

    @if($page_owner)
    <div class="container row" style="background:rgba(240,255,248,0.6);">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body" style="width:20vw">
                <div class="card-title" style="text-align:center;">
                    <h4>Bookings Awaiting Approval:</h4>
                </div>
                <hr>
                <div class="card-text card">
                @if(count($aa_bookings) > 0)
                    @foreach($aa_bookings as $b)
                                <div class="row">
                                    <div class="col-sm-12 col-md-12 col-lg-12">
                                        <div>
                                            <div>Guest: <a href="/user_profile/{{$b->id}}">{{$b->name}}</a>
                                                @if($b->scores > 2.5)
                                                    <i class="fas fa-star gold-stpar"></i>&nbsp;{{$b->scores}}
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
                                            <div style="margin-top:5px;"><span class="btn btn-success" name="approve_booking" data-id="{{$b->booking_id}}">Approve Booking</span></div>
                                            <div style="margin-top:5px;"><span class="btn btn-danger" name="deny_booking" data-id="{{$b->booking_id}}" style="color:white">Deny Booking</span></div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                    @endforeach
                @else
                    <div>You have no bookings to approve.</div>
                @endif
                </div>
            </div>
        </div>
        <div class="row" style="width:20vw">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body">
                <div class="card-title" style="text-align:center;">
                    <h4>Upcoming Approved Bookings:</h4>
                </div>
                <hr>
                <div class="card-text card">
                @if(count($ua_bookings) > 0)
                    @foreach($ua_bookings as $b)
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
                    <div>You have no upcoming approved bookings.</div>
                @endif
                </div>
            </div>
        </div>
        <div class="row" style="width:20.5vw">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body">
                <div class="card-title" style="text-align:center;">
                    <h4> Past Approved Bookings:</h4>
                </div>
                <hr>
                <div class="card-text card">
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
                    <div>You have no past approved bookings.</div>
                @endif
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="container row" style="background:rgba(240,255,248,0.6)">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <b><h2>User Reviews</h2></b><hr>
            @if(count($reviews) > 0)
                @foreach($reviews as $r)
                <div class="row card card-text" align="left">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div>Score: {{$r->trs_score}}</div>
                        <div>Review: {{$r->trs_review}}</div>
                        <div>Submitted At: {{$r->trs_submitted_at}}</div>
                        <div>Submitted by <a href="/user_profile/{{$r->id}}">{{$r->name}}</a></div>
                        @if($r->trs_edited == 1)<div>Edited at {{$r->trs_edited_at}}</div>@endif
                    </div>
                </div>
                <hr>
                @endforeach
            @else
                <div>No reviews submitted for this user</div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
        var booking_id;
        var start_date;

        $(document).on('click', '[name="delete_booking"]', function(){

            booking_id = $(this).data('id');
            $.ajax({
                url: '/cancel_booking',
                method: 'POST',
                data: 'booking_id='+booking_id,
                success: function(html) {
                    var data = JSON.parse(html);
                    if(data['status'] == "success") {
                        Swal.fire("Success", "Booking Cancelled Successfully", "success");
                    } else if(data['status'] == 'date error') {
                        Swal.fire("Warning", "You cannot cancel a booking scheduled in the next 2 weeks.", "warning");
                    } else {
                        Swal.fire("Error", "There was an error, please try again!", "error");
                    }
                    location.reload();
                },
            });
        });

        $(document).on('click', '[name="approve_booking"]', function(e) {
                e.preventDefault();
                var id = $(this).data('id');

                $.ajax({
                    url: '/approve_booking/'+id,
                    method: 'GET',
                    dataType: 'JSON',
                    success: function(html) {
                        if(html['status'] == "success") {
                            swal({
                                title:"Success!",
                                text: "Booking approved successfully",
                                type:"success",
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 3000);
                        } else if(html['status'] == 'overlapping_bookings') {
                            Swal.fire("Warning", "An approved booking overlaps with this booking. This booking has been denied", "warning");
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
});
</script>
@endsection
