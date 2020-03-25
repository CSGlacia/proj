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
            <a class="btn btn-success" name="book_property" data-id="{{$p->property_id}}"> Book property </a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <h2><b>Reviews:</b></h2>
            <hr>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
</script>
@endsection
