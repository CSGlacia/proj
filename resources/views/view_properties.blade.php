@extends('layouts.app')

@section('content')
<div class="container">
    <div class="search-container">
      <div class="search-container">
        <form action="/view_properties" method="GET"> <!--CHANGE TO SEARCH LOGIC-->
          <input type="text" placeholder="Search by name..." name="query" size="100">
          <input type="submit" value="Search" /><br>
          <input type="checkbox" name="address_checkbox" value="1">
          <label for="address_checkbox"> Address </label>
          <input type="checkbox" name="suburb_checkbox" value="1">
          <label for="suburb_checkbox"> Suburb </label>
          <input type="checkbox" name="postcode_checkbox" value="1">
          <label for="postcode_checkbox"> Postcode </label>
        </form>

      </div>
      @foreach ($properties as $p)
        <hr>
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <h3><b> {{ $p->property_title }}</b></h3>
                <div>Address: {{ $p->property_address }}</div>
                <div>Suburb: {{ $p->property_suburb }}, {{ $p->property_postcode}} </div>
                <span class="label bg-primary"> Beds: {{ $p->property_beds }} </span>
                <span class="label bg-warning"> Bathrooms: {{ $p->property_baths }} </span>
                <span class="label bg-danger"> Cars: {{ $p->property_cars }} </span>
                <div> {{ $p->property_desc }}  </div>
                <a class="btn btn-primary" name="view_property" href="/view_property/{{$p->property_id}}"> View property </a>
            </div>
        </div>
      @endforeach
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

});
</script>
@endsection
