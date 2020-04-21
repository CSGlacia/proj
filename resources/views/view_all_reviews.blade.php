@extends('layouts.app')

@section('content')
<div class="container" style="background:rgba(240,255,248,0.6)">
    <h1>Property Reviews</h1>
    <hr>
    <div class="container row" style="margin:auto">
        @foreach ($property_review as $r)
            <div class="row" align="center">
                <div class="col-sm-9 col-md-9 col-lg-9 card" style="margin:0px; width:25vw;">
                    <h4><b>Review ID: {{ $r['review_id'] }}</b></h4>
                    <h5>Booking ID: </h5><span> {{ $r['booking_id'] }}</span>
                    <h5>Reviewer Name: </h5><span>{{$r['reviewer_name']}}</span>
                    <h5> Property Name:  </h5><span>{{ $r['property_name'] }}  </span>
                    <a class="btn btn-danger" style="margin:5px; color:white" name="delete_property_review" data-id="{{$r['review_id']}}"> Delete review</a>
              </div>
          </div>
          <hr>
        @endforeach
    </div>
    <h1>Tennant Reviews </h1>
    <hr>
    <div class="container row" style="margin:auto">
        @foreach ($tennant_review as $r)
            <div class="row">
                <div class="col-sm-9 col-md-9 col-lg-9 card" style="margin:0px; width:25vw;">
                    <h4><b>Review ID: {{ $r['review_id'] }}</b></h4>
                    <h5>Reviewer Name: </h5><span>{{$r['reviewer_name']}}</span>
                    <h5> Tennant Name: </h5><span>{{ $r['property_name'] }}  </span>
                    <h5>Booking ID: </h5><span>{{ $r['booking_id'] }}</span>
                    <a class="btn btn-danger" style="margin:5px; color:white" name="delete_tennant_review" data-id="{{$r['review_id']}}"> Delete review</a>
                </div>
            </div>
            <hr>
        @endforeach
    </div>

</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
        var review_id;

        $(document).on('click', '[name="delete_tennant_review"]', function(){

            review_id = $(this).data('id');
            $.ajax({
                url: '/admin_delete_tennant_review',
                method: 'POST',
                data: 'review_id='+review_id,
                success: function(html) {
                    var data = JSON.parse(html);
                    if(data['status'] == "success") {
                        Swal.fire("Success", "Review Deleted Successfully", "success");
                    } else {
                        Swal.fire("Error", "There was an error, please try again!", "error");
                    }
                    location.reload();
                },
            });
        });
        $(document).on('click', '[name="delete_property_review"]', function(){

            review_id = $(this).data('id');
            $.ajax({
                url: '/admin_delete_property_review',
                method: 'POST',
                data: 'review_id='+review_id,
                success: function(html) {
                    var data = JSON.parse(html);
                    if(data['status'] == "success") {
                        Swal.fire("Success", "Review Deleted Successfully", "success");
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
