@extends('layouts.app')

@section('content')
<div class="container">
    <div class="search-container">
      <div class="search-container">
        <form action="/view_property" method="GET"> <!--CHANGE TO SEARCH LOGIC-->
          <input type="text" placeholder="Search by name..." name="query" size="100">
          <input type="submit" value="Search" />
        </form>
      </div>

      @foreach ($properties as $p)
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
                <b> <h3> {{ $p->property_address }} </h3> </b>
                <div> Suburb: {{ $p->property_suburb }}, {{ $p->property_postcode}} </div>
                <span class="label bg-primary"> Beds: {{ $p->property_beds }} </span>
                <span class="label bg-warning"> Bathrooms: {{ $p->property_baths }} </span>
                <span class="label bg-danger"> Cars: {{ $p->property_cars }} </span>
                <div> {{ $p->property_desc }}  </div>
                <a class="btn btn-primary" name="view_property" data-id="{{$p->property_id}}"> View property </a>
            </div>
        </div>
    @endforeach
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '[name ="view_property"]', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        console.log(id);
    });
});
</script>
@endsection
