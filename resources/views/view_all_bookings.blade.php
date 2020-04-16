@extends('layouts.app')

@section('content')
<div class="container">
    @foreach ($results as $r)
      <hr>
      <div class="row">
          <div class="col-sm-12 col-md-12 col-lg-12">
              <h3><b>Booking ID: {{ $r['booking_id'] }}</b></h3>
              <h4> Property Name:  {{ $r['property_title'] }}  </h4>
              <h5>Booking Start Date: {{ $r['booking_startDate'] }}</h5>
              <h5>Booking Start Date: {{ $r['booking_endDate'] }}</h5>
              <a class="btn btn-danger" name="delete_booking" data-id="{{$r['booking_id']}}"> Cancel booking</a>
          </div>
      </div>
    @endforeach

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
