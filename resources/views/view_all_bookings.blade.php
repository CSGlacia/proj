@extends('layouts.app')

@section('content')
<div class=container style="background:rgba(240,255,248,0.6)">
    <h1>All Bookings</h1>
    <hr>
    <div class="container row" style="margin:auto">
        @foreach ($results as $r)
          <hr>
          <div class="row" align="center">
              <div class="col-sm-9 col-md-9 col-lg-9 card" style="margin:0px; width:25vw;">
                    <h4><b>Booking ID: {{ $r['booking_id'] }}</b></h4>
                    <h5>Username:</h5><span> {{$r['username']}}</span>
                    <h5> Property Name:</h5><span>{{ $r['property_title'] }}</span>
                    <h5>Booking Start Date:</h5><span> {{ $r['booking_startDate'] }}</span>
                    <h5>Booking Start Date: </h5><span>{{ $r['booking_endDate'] }}</span>
                    <a class="btn btn-danger"  style="margin:5px; color:white" name="delete_booking" data-id="{{$r['booking_id']}}"> Cancel booking</a>
              </div>
          </div>
        @endforeach

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
                url: '/admin_delete_bookings',
                method: 'POST',
                data: 'booking_id='+booking_id,
                success: function(html) {
                    var data = JSON.parse(html);
                    if(data['status'] == "success") {
                        Swal.fire("Success", "Booking Cancelled Successfully", "success");
                    } else {
                        Swal.fire("Error", "There was an error, please try again!", "error");
                    }
                    location.reload();
                },
            });
        });
});
</script>
@endsection
