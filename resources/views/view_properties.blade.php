@extends('layouts.app')

@section('content')
<div class="hs-slider owl-carousel">
    <div class="hs-item set-bg" data-setbg="https://turtle-database.s3-ap-southeast-2.amazonaws.com/background/city-skyline-across-body-of-water-during-night-time-3586966.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="hs-text">
                        <h2>Photography Studio</h2>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                            incididunt ut labore et<br /> dolore magna aliqua. Quis ipsum suspendisse ultrices
                            gravida accumsan lacus vel facilisis.</p>
                        <a href="#" class="primary-btn">Contact us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hs-item set-bg" data-setbg="https://turtle-database.s3-ap-southeast-2.amazonaws.com/background/city-skyline-across-body-of-water-during-night-time-3586966.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="hs-text">
                        <h2>Photography Studio</h2>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                            incididunt ut labore et<br /> dolore magna aliqua. Quis ipsum suspendisse ultrices
                            gravida accumsan lacus vel facilisis.</p>
                        <a href="#" class="primary-btn">Contact us</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
          <input type="checkbox" name="suburb_checkbox" value="1" style="margin-left:30px">
          <label for="suburb_checkbox"> Suburb </label>
          <input type="checkbox" name="postcode_checkbox" value="1" style="margin-left:30px">
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
                  <div>{{ $p->property_address }} </div>
                  <div style="margin:5px;"> {{ $p->property_desc }}  </div>
                </div>
            </div>
        </div>
      @endforeach
</div>
@endsection

@section('scripts')
<script>
$('.set-bg').each(function () {
    var bg = $(this).data('setbg');
    $(this).css('background-image', 'url(' + bg + ')');
    $(this).css('height', 'auto');
});
var hero_s = $(".hs-slider");
hero_s.owlCarousel({
    loop: true,
    margin: 0,
    nav: true,
    items: 1,
    dots: false,
    animateOut: 'fadeOut',
    animateIn: 'fadeIn',
    navText: ['<span class="arrow_carrot-left"></span>', '<span class="arrow_carrot-right"></span>'],
    smartSpeed: 1200,
    autoHeight: false,
    autoplay: true
});
$(document).ready(function() {
    $(document).on('click', '[name="view_property"]', function() {
        window.location.href = '/view_property/'+$(this).data('id');
    });
});
</script>
@endsection
