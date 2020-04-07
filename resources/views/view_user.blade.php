@extends('layouts.app')

@section('content')
<div class="container">
    <span class="label bg-primary">Name: {{$user->name}}</span>
    <span class="label bg-warning">Email: {{$user->email}}</span>
    <span class="label bg-secondary">Guest rating: </span>
    <span>{{$guest_score}}</span>
    <span name="score-star"><i class="fas fa-star gold-star"></i></span>

    @if($page_owner)
    <div class="row">
        <b><h2>Bookings:</h2></b>
        <div class="col-sm-12 col-md-12 col-lg-12">
            @foreach($bookings as $b)
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12 card-body">
                        <div class="card-title">
                            <h3>{{ $b->property_title }}</h3>
                        </div>
                        <div>
                            <div>Address: {{$b->property_address}}</div>
                            <div>Suburb: {{$b->property_suburb}}</div>
                            <div>Persons: {{$b->booking_persons}}</div>
                        </div>
                        <span>Start Date: {{$b->booking_startDate}}</span>
                        <span>End Date: {{$b->booking_endDate}}</span>
                        <a class="btn btn-primary" name="view_booking" data-id="{{$b->booking_id}}"> View booking</a>
                        <a class="btn btn-warning" name="delete_booking" data-id="{{$b->booking_id}}"> Cancel booking</a>
                    </div>
                    <hr>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    <div class="row">
        <b><h2>Properties:</h2></b>
        <hr>
        <div class="col-sm-12 col-md-12 col-lg-12">
          @foreach ($properties as $p)
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <h3><b> {{ $p->property_title }}</b></h3>
                    <div>Address: {{ $p->property_address }}</div>
                    <span> <i class="fas fa-bed"></i>&nbsp;{{ $p->property_beds }} </span>
                    <span> <i class="fas fa-toilet"></i>&nbsp;{{ $p->property_baths }} </span>
                    <span> <i class="fas fa-car"></i>&nbsp;{{ $p->property_cars }} </span>
                    <div> {{ $p->property_desc }}  </div>
                    <a class="btn btn-primary" name="view_property" href="/view_property/{{$p->property_id}}"> View property </a>
                </div>
            </div>
            <hr>
          @endforeach
        </div>
    </div>
        <div class="row">
            <b><h2>Listings:</h2></b>
            <hr>
            <div class="col-sm-12 col-md-12 col-lg-12">
              @foreach ($listings as $l)
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <h3><b> {{ $l->property_title }}</b></h3>
                        <div>Address: {{ $l->property_address }}</div>
                        <div>Start Date: {{ $l->start_date }}  </div>
                        <div>End Date: {{ $l->end_date }}  </div>
                        <a class="btn btn-primary" name="view_property" href="/view_property/{{$l->property_id}}"> View property </a>
                    </div>
                </div>
                <hr>
              @endforeach
          </div>
    @if($page_owner == true)
        <a href="/create_property_listing" class="btn btn-primary">Create a property listing</a>
    @endif
    </div>
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
                        alert("Booking Cancelled");
                    } else if(data['status'] == 'date error') {
                        alert("You cannot cancel a booking scheduled in the next 2 weeks.");
                    } else {
                        alert("There was an error, please try again!");
                    }
                    location.reload();
                },
            });
        });
});
</script>
@endsection
