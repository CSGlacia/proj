@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Property Reviews</h1>
    <hr>
    @foreach ($property_review as $r)
      <hr>
      <div class="row">
          <div class="col-sm-12 col-md-12 col-lg-12">
                <h3><b>Review ID: {{ $r['review_id'] }}</b></h3>
                <h4>Reviewer Name: {{$r['reviewer_name']}}</h4>
                <h4> Property Name:  {{ $r['property_name'] }}  </h4>
                <h4>Booking ID: {{ $r['booking_id'] }}</h4>
                <a class="btn btn-danger" name="delete_review" data-id="{{$r['review_id']}}"> Delete review</a>
          </div>
      </div>
    @endforeach
    <hr>
    <h1>Tennant Reviews </h1>
    <hr>
    @foreach ($tennant_review as $r)
        <hr>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <h3><b>Review ID: {{ $r['review_id'] }}</b></h3>
                <h4>Reviewer Name: {{$r['reviewer_name']}}</h4>
                <h4> Tennant Name:  {{ $r['property_name'] }}  </h4>
                <h4>Booking ID: {{ $r['booking_id'] }}</h4>
                <a class="btn btn-danger" name="delete_review" data-id="{{$r['review_id']}}"> Delete review</a>
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
