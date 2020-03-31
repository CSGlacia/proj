@extends('layouts.app')

@section('content')
<div class="container">
     <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <h2><b>{{$p->property_title}}</b></h2>
            <hr>
            <div> Address: {{$p->property_address}}</div>
            <div> Suburb: {{ $p->property_suburb }}, {{ $p->property_postcode}} </div>
            <span class="label bg-primary"> Beds: {{ $p->property_beds }} </span>
            <span class="label bg-warning"> Bathrooms: {{ $p->property_baths }} </span>
            <span class="label bg-danger"> Cars: {{ $p->property_cars }} </span>
            <div> {{ $p->property_desc }}  </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <h2><b>Current Availabilities</b></h2>
            <hr>
            @if(count($avail) > 0)
                @foreach ($avail as $a)
                    <h4> {{ $a->booking_startDate }} - {{$a->booking_endDate}} </h4>
                @endforeach
            @else
                <h4> Everything is available!</h4>
            @endif
        </div>
    </div>

        @auth
        <div id="user_logged" data-logged="1" hidden></div>
        @else
        <div id="user_logged" data-logged="0" hidden></div>
        @endif

    <div class="row">
        <!--CODE TO CHECK FOR LOGIN, IF USER ISNT LOGGED IN WE ALERT USER AND DONT SEND REQUEST TO BOOK -->

        <div class="card col-sm-12 col-md-12 col-lg-12">
        <br>
        <b><h3 style="text-align:center;">Make a Booking</h3></b>
        <hr>
        <div id="listing_form" class="form-group">
            <h5>Booking Details:&nbsp;</h5>
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
                    <span>(!) Number of People:&nbsp;</span>
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

    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <h2><b>Reviews:</b></h2>
            <hr>
            @if(count($reviews) > 0)
                @foreach($reviews as $r)
                <div class="row">
                    <div class="col-sm-6 col-md-6 col-lg-6">
                        <div>Score: {{$r->prs_score}}</div>
                        <div>Review: {{$r->prs_review}}</div>
                        <div>Submitted At: {{$r->prs_submitted_at}}</div>
                        <div>Submitted by <a href="/user_profile/{{$r->id}}">{{$r->name}}</a></div>
                    </div>
                </div>
                <hr>
                @endforeach
            @else
                <div>No reviews submitted for this property</div>
            @endif
        </div>
    </div>

    <!-- Check for the correct user to be logged on -->
    <a id="delete_property" class="btn btn-primary">✖ Delete Property</a>

</div>
@endsection

@section('scripts')
<script>
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
                    } else {
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
                    var data = JSON.parse(html);
                    if(data['status'] == "success") {
                        alert("Success!");
                    } else {
                        alert("There was an error, please try again!");
                    }
                },
                error: function ( xhr, errorType, exception ) {
                    var errorMessage = exception || xhr.statusText;
                    alert("There was a connectivity problem. Please try again.");
                }
            });
        } else {
            alert("An error occurred! (You shouldn't see this)");
        }
    });
});
</script>
@endsection
