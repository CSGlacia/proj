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
      <?php
      $results = DB::table('properties AS p')
                  ->select('*') //columns to select
                  ->where([
                        ['p.property_suburb', 'LIKE', "test"]
                    ])
                  ->get(); //finishes the query, and will grab all relevant results, can also use first() to
                      //grab a single result

        //iterate through results as so:
        $result_arr = [];
        foreach($results as $r) {
            $result_arr[] = ['id' => $r->property_id];
        }
        //this will build an array iterating over each result. $r is the single result
        //object, and use ->column_name to acces the column variables
      ?>
      @foreach ($results as $p)
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
          </div>}
      @endforeach
    </div>


    <!--@foreach ($properties as $p)
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
    @endforeach-->
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
