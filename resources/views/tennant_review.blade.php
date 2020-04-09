@extends('layouts.app')

@section('content')
<div class="container row">
    <div class="col-sm-6 col-md-6 col-lg-6">
        <h1>Incomplete Tennant Reviews</h1>
        @if(count($bookings) > 0)
            @foreach($bookings as $b)
            <div class="row card item-card cursor-pointer" name="review_tennant" data-prop-id="{{$b->property_id}}" data-booking-id="{{$b->booking_id}}" style="margin:0px; border:none;">
                <div class="col-sm-12 col-md-12 col-lg-12 card-body">
                    <div class="card-title">
                        <a href="/user_profile/{{$b->booking_userID}}">{{$b->name}}</a>
                    </div>
                    <div class="card-text">
                        <div>{{$b->booking_persons}} Person(s) at {{$b->property_address}} from  {{$b->booking_startDate}} to {{$b->booking_endDate}}</div>
                    </div>
                </div>
                <a class="btn btn-primary" name="review_tennant" href="/review_tennant?booking_id={{$b->booking_id}}&prop_id={{$b->property_id}}"> Review tenant</a>
            </div>
            @endforeach
        @else 
            <div>You have no tennants to review</div>
        @endif
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6">
        <h1>Submitted Tennant Reviews</h1>
        @if(count($past_reviews) > 0)
            @foreach($past_reviews as $p)
            <div class="row card item-card cursor-pointer" name="edit_review" data-review-id="{{$p->trs_id}}" style="margin:0px; border:none;">
                <div class="col-sm-12 col-md-12 col-lg-12 card-body">
                    <div class="card-title">
                        <a href="/user_profile/{{$p->booking_userID}}">{{$p->name}}</a>
                    </div>
                    <div class="card-text">
                        <div>{{$p->booking_persons}} Person(s) at {{$p->property_address}} from  {{$p->booking_startDate}} to {{$p->booking_endDate}}</div>
                        <hr>
                        <i class="fas fa-star gold-star"></i>{{$p->trs_score}}
                        <div>{{$p->trs_review}}</div>
                    </div>
                </div>
                <a class="btn btn-warning" href="/edit_tennant_review/{{$p->trs_id}}">Edit Review</a>
            </div>
            @endforeach
        @else 
            <div>You have no tennants to review</div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '[name="review_property"]', function() {
        window.location.href = '/review_tennant?'+'booking_id='+$(this).data('booking-id')+'&prop_id='+$(this).data('prop-id');
    });

    $(document).on('click', '[name="edit_review"]', function() {
        window.location.href = '/edit_tennant_review/'+$(this).data('review-id');
    });
});
</script>
@endsection
