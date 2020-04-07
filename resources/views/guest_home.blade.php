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


@endsection

@section('scripts')
<script>
    $('.set-bg').each(function () {
        var bg = $(this).data('setbg');
        $(this).css('background-image', 'url(' + bg + ')');
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

</script>
@endsection