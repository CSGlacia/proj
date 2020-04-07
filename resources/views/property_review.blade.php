@extends('layouts.app')

@section('content')
<div class="container col-sm-12 col-md-12 col-lg-12">
    <h1>Property Reviews</h1>
    @if(count($bookings) > 0)    
        @foreach($bookings as $b)
        <div class="row card item-card cursor-pointer" name="review_property" data-prop-id="{{$b->property_id}}" data-booking-id="{{$b->booking_id}}" style="margin:0px; border:none;">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body" >
                <div class="card-title">
                  <h3>{{ $b->property_title }}</h3>
                </div>
                <div class="card-text">
                  <div style="margin:5px;">
                    <span><i class="fas fa-bed"></i>&nbsp;{{ $b->property_beds }} </span>
                    <span><i class="fas fa-bath"></i>&nbsp;{{ $b->property_baths }} </span>
                    <span><i class="fas fa-car"></i>&nbsp;{{ $b->property_cars }} </span>
                  </div>
                  <div>{{ $b->property_address }}</div>
                  <div style="margin:5px;"> {{ $b->property_desc }}  </div>
                  <hr>
                  <span>{{$b->booking_startDate}} - {{$b->booking_endDate}}</span>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div>You have no past bookings to review</div>
    @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '[name="review_property"]', function() {
        window.location.href = '/review_property?prop_id='+$(this).data('prop-id')+'&booking_id='+$(this).data('booking-id');
    });
});
</script>
@endsection

