@extends('layouts.app')

@section('content')
<div class="container">
    @foreach ($property as $p)
        <b> <h2> {{ $p->property_address }} </h2> </b>
                    <div> Suburb: {{ $p->property_suburb }}, {{ $p->property_postcode}} </div>
                    <span class="label bg-primary"> Beds: {{ $p->property_beds }} </span>
                    <span class="label bg-warning"> Bathrooms: {{ $p->property_baths }} </span>
                    <span class="label bg-danger"> Cars: {{ $p->property_cars }} </span>
                    <div> {{ $p->property_desc }}  </div>
    @endforeach



    <h2> Current Availabilities</h2>
    @if(count($avail) > 0)
        @foreach ($avail as $a)
            <h4> {{ $a->startDate }} - {{$a->endDate}} </h4> 
        @endforeach
    @else
        <h4> Everything is available!</h4>
    @endif


    <a class="btn btn-success" name="book_property" data-id="{{$p->property_id}}"> Book property </a>
</div>
@endsection

@section('scripts')
<script>
</script>
@endsection
