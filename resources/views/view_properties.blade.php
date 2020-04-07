@extends('layouts.app')

@section('content')



<div class="container col-sm-10 col-md-10 col-lg-10">
    <div class="search-container">
        <form action="/view_properties" method="GET">
            <div class="input-group container col-sm-10 col-md-10 col-lg-9">


              <input type="text" class="form-control" placeholder="Search by name..." name="query" size="100">
              <div class="input-group-append">
                  <button type="submit" class="btn btn-secondary" style="background-color: #00B36B">
                    <i class="fa fa-search"></i>
                </button>
              </div>
          </div>
          <input type="checkbox" name="address_checkbox" value="1" style="margin-left:25vh">
          <label for="address_checkbox"> Address </label>
          <input type="checkbox" name="suburb_checkbox" value="1">
          <label for="suburb_checkbox"> Suburb </label>
          <input type="checkbox" name="postcode_checkbox" value="1">
          <label for="postcode_checkbox"> Postcode </label>

        </form>

    </div>
      @foreach ($properties as $p)
        <div class="row card item-card cursor-pointer" name="view_property" data-id="{{$p->property_id}}" style="margin:0px; border:none;">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body" >
                <div class="card-title">
                  <h3>{{ $p->property_title }}</h3>
                </div>
                <div class="card-text">
                  <div style="margin:5px;">
                    <span><i class="fas fa-bed"></i>&nbsp;{{ $p->property_beds }} </span>
                    <span><i class="fas fa-toilet"></i>&nbsp;{{ $p->property_baths }} </span>
                    <span><i class="fas fa-car"></i>&nbsp;{{ $p->property_cars }} </span>
                  </div>
                  <div>{{ $p->property_address }}, {{ $p->property_suburb }}, {{ $p->property_postcode}} </div>
                  <div style="margin:5px;"> {{ $p->property_desc }}  </div>
                </div>
            </div>
        </div>
      @endforeach
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '[name="view_property"]', function() {
        window.location.href = '/view_property/'+$(this).data('id');
    });
});
</script>
@endsection
