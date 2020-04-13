@extends('layouts.app')

<link href="{{asset('css/view_property.css')}}" rel="stylesheet">
@section('content')
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

<div class="hs-slider owl-carousel">
    <div class="hs-item set-bg" data-setbg="https://turtle-database.s3-ap-southeast-2.amazonaws.com/background/city-skyline-across-body-of-water-during-night-time-3586966.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="hs-text">
                        <h2>Turtle</h2>
                        <p>Would you like to book a place at turtle?
                        <br /> Try it below!</p>
                        <a type="button" href="/" class="btn btn-primary btn-lg">Click here to look at the available properties</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hs-item set-bg" data-setbg="https://turtle-database.s3-ap-southeast-2.amazonaws.com/background/Houses_in_Sanctuary_Cove_seen_from_Coomera_River%2C_Queensland_09.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="hs-text">
                        <h2>Turtle</h2>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                            incididunt ut labore et<br /> dolore magna aliqua. Quis ipsum suspendisse ultrices
                            gravida accumsan lacus vel facilisis.</p>
                        <a type="button" href="/" class="btn btn-primary btn-lg">Click here to look at the available properties</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<section class="services-section spad">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="services-item">
                    <img src="https://turtle-database.s3-ap-southeast-2.amazonaws.com/background/city-skyline-across-body-of-water-during-night-time-3586966.jpg" alt="">
                    <h3>Shooting</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                        magna aliqua. Quis ipsum suspendisse ultrices gravida.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="services-item">
                    <img src="https://turtle-database.s3-ap-southeast-2.amazonaws.com/background/city-skyline-across-body-of-water-during-night-time-3586966.jpg" alt="">
                    <h3>Videos</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                        magna aliqua. Quis ipsum suspendisse ultrices gravida.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6">
                <div class="services-item">
                    <img src="https://turtle-database.s3-ap-southeast-2.amazonaws.com/background/city-skyline-across-body-of-water-during-night-time-3586966.jpg" alt="">
                    <h3>Editing</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                        magna aliqua. Quis ipsum suspendisse ultrices gravida.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
$('.set-bg').each(function () {
    var bg = $(this).data('setbg');
    $(this).css('background-image', 'url(' + bg + ')');
    $(this).css('background-size', 'cover');    
    $(this).css('height','50vh');
});
var hero_s = $(".hs-slider");
//need to move this later to css
hero_s.css('width','100vh');
hero_s.css('position','relative');
hero_s.css('left','25%');
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
</script>
@endsection