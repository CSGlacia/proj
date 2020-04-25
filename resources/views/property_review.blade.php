@extends('layouts.app')
<link href="{{asset('css/review.css')}}" rel="stylesheet">
@section('content')
<div class="container row" style="position:absolute;left:1vw">
    <div class="col-sm-6 col-md-6 col-lg-6" style="background:rgba(255,155,155,0.3)">
    <h1>Incomplete Property Reviews</h1>
    @if(count($bookings) > 0)
        @foreach($bookings as $b)
        <div class="row card item-card cursor-pointer" name="review_property" data-prop-id="{{$b->property_id}}" data-booking-id="{{$b->booking_id}}" style="margin:5px; border:none;">
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
                  <div><i class="fas fa-user"></i>&nbsp;<a href="/user_profile/{{$b->id}}">{{$b->name}}</a></div>
                  <hr>
                  <span>{{$b->booking_persons}} Person(s) from {{$b->booking_startDate}} to {{$b->booking_endDate}}</span>
                </div>
            </div>
            <div align="center">
                <a class="btn btn-primary col-sm-10 col-md-10 col-lg-10" name="review_property" href="/review_property?prop_id={{$b->property_id}}&booking_id={{$b->booking_id}}"> Review Property</a>
            </div>
        </div>
        @endforeach
    @else
        <div>You have no past bookings to review</div>
    @endif
    </div>

    <div class="col-sm-6 col-md-6 col-lg-6" style="background:rgba(135,255,155,0.3);position:absolute;right:-1vw">
    <h1>Submitted Property Reviews</h1>
    @if(count($reviews) > 0)
        @foreach($reviews as $r)
        <div class="row card item-card cursor-pointer" name="edit_review" data-review-id="{{$r->prs_id}}" style="margin:5px; border:none;">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body" >
                <div class="card-title">
                  <h3>{{ $r->property_title }}</h3>
                </div>
                <div class="card-text">
                  <div style="margin:5px;">
                    <span><i class="fas fa-bed"></i>&nbsp;{{ $r->property_beds }} </span>
                    <span><i class="fas fa-bath"></i>&nbsp;{{ $r->property_baths }} </span>
                    <span><i class="fas fa-car"></i>&nbsp;{{ $r->property_cars }} </span>
                  </div>
                  <div>{{ $r->property_address }}</div>
                  <div style="margin:5px;"> {{ $r->property_desc }}  </div>
                  <div><i class="fas fa-user"></i>&nbsp;<a href="/user_profile/{{$r->id}}">{{$r->name}}</a></div>
                  <hr>
                  <span>{{$r->booking_persons}} Person(s) from {{$r->booking_startDate}} to {{$r->booking_endDate}}</span>
                </div>
            </div>
        </div>
        <!--
        <div align="center">
                    <a class="btn btn-warning col-sm-10 col-md-10 col-lg-10" href="/edit_property_review/{{$r->prs_id}}">Edit Review</a>
        </div>
      -->
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
/*
    $(document).on('click', '[name="edit_review"]', function() {
        window.location.href = '/edit_property_review/'+$(this).data('review-id');
    });
*/
});
</script>
@endsection
