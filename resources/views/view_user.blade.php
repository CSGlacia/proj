@extends('layouts.app')

@section('content')
<div class="container">
    <span class="label bg-primary">Name: {{$user->name}}</span>
    <span class="label bg-warning">Email: {{$user->email}}</span>
    <span class="label bg-secondary">Guest rating: </span>
    <span>{{$guest_score}}</span>
    <span name="score-star"><i class="fas fa-star gold-star"></i></span>

    @if($page_owner)
    <div class="row card">
        <b><h2>Your Unapproved Bookings:</h2></b>
        <div class="col-sm-12 col-md-12 col-lg-12">
            @foreach($bookings as $b)
                <div class="row">
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
                        <a class="btn btn-primary" name="view_booking" href="/view_booking/{{$b->booking_id}}" data-id="{{$b->booking_id}}"> View booking</a>
                        <a class="btn btn-danger" name="delete_booking" data-id="{{$b->booking_id}}"> Cancel booking</a>
                    </div>
                    <hr>
                </div>
            @endforeach
        </div>
    </div>
    <div class="row card">
        <b><h2>Your Approved Bookings:</h2></b>
        <div class="col-sm-12 col-md-12 col-lg-12">
            @foreach($abookings as $b)
                <div class="row">
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
                        <a class="btn btn-primary" name="view_booking" href="/view_booking/{{$b->booking_id}}" data-id="{{$b->booking_id}}"> View booking</a>
                        <a class="btn btn-danger" name="delete_booking" data-id="{{$b->booking_id}}"> Cancel booking</a>
                    </div>
                    <hr>
                </div>
            @endforeach
        </div>
    </div>
    <div class="row">
        <b><h2>Your Past Bookings:</h2></b>
        <div class="col-sm-12 col-md-12 col-lg-12">
            @foreach($pbookings as $b)
                <div class="row">
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
                        <a class="btn btn-primary" name="view_booking" href="/view_booking/{{$b->booking_id}}" data-id="{{$b->booking_id}}"> View booking</a>
                    </div>
                    <hr>
                </div>
            @endforeach
        </div>
    </div>
    <div class="row">
        <b><h2>Your Denied Bookings:</h2></b>
        <div class="col-sm-12 col-md-12 col-lg-12">
            @foreach($dbookings as $b)
                <div class="row">
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
                        <a class="btn btn-primary" name="view_booking" href="/view_booking/{{$b->booking_id}}" data-id="{{$b->booking_id}}"> View booking</a>
                    </div>
                    <hr>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="row card">
        <b><h2>Properties:</h2></b>
        <hr>
        <div class="col-sm-12 col-md-12 col-lg-12">
      @foreach ($properties as $p)
        <div class="row card item-card cursor-pointer" name="view_property" data-id="{{$p->property_id}}" style="margin:0px; border:none;">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body" >
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
                </div>
                <a class="btn btn-primary" name="view_property" href="/view_property/{{$p->property_id}}"> View property </a>
                    <a class="btn btn-warning" name="edit_property" href="/edit_property/{{$p->property_id}}"> Edit property </a>
            </div>
        </div>
      @endforeach
        </div>
    </div>

    @if($page_owner)
        <div class="row card">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body">
                <div class="card-title" style="text-align:center;">
                    <h2>Bookings Awaiting Your Approval:</h2>
                </div>
                <hr>
                <div class="card-text">
                @if(count($aa_bookings) > 0)
                    @foreach($aa_bookings as $b)
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
                                            <div style="margin-top:5px;"><span class="btn btn-success" name="approve_booking" data-id="{{$b->booking_id}}">Approve Booking</span>&nbsp;<span class="btn btn-warning" name="deny_booking" data-id="{{$b->booking_id}}">Deny Bookying</span></div>
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
    @endif

    <div class="row">
        <b><h2>User Reviews</h2></b>
        <div class="col-sm-12 col-md-12 col-lg-12">
            @if(count($reviews) > 0)
                @foreach($reviews as $r)
                <div class="row">
                    <div class="col-sm-6 col-md-6 col-lg-6">
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
