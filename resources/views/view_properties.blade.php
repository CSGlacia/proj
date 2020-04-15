@extends('layouts.app')
<link href="{{asset('css/view_property.css')}}" rel="stylesheet">
@section('content')


<div class="container col-sm-10 col-md-10 col-lg-10">
    <div class="search-container row card">
        <div class="card-title" style="margin:15px; margin-bottom:0px;">
            <h2>Search Properties</h2>
        </div>
        <hr>
        <div class="card-text">
            <h4 style="margin-left:15px;">Property Details</h4>
            <div class="row col-sm-12 col-md-12 col-lg-12">
                <div class="col-sm-4 col-md-4 col-lg-4">
                    <span>Property Name:&nbsp;</span>
                    <input id="prop_name" class="form-control" type="text" placeholder="E.g. Beautiful Beach House">
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4">
                    <span>Property Address:&nbsp;</span>
                    <input id="prop_address" class="form-control" type="text" placeholder="E.g. 129 Holt Road">
                </div>  
                <div class="col-sm-2 col-md-2 col-lg-2">
                    <span>Property Suburb:&nbsp;</span>
                    <select id="prop_suburb" class="form-control" name="suburbs[]" multiple>
                        @foreach($suburbs as $s)
                            <option value="{{$s->property_suburb}}">{{$s->property_suburb}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row col-sm-12 col-md-12 col-lg-12" style="margin-bottom:30px;">
                <div class="col-sm-4 col-md-4 col-lg-4">
                    <div>Tags:&nbsp;</div>
                    <select id="tags" class="form-control" name="tags[]" multiple>
                        @foreach($tags as $t)
                            <option value="{{$t['id']}}">{{$t['text']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-2 col-md-2 col-lg-2">
                    <div style="margin-bottom:5px;">Minimum Rating:</div>
                    <span>
                        <span name="score-star"><i class="fas fa-star gold-star" id="s1" data-num="1"></i></span>
                        <span name="score-star"><i class="fas fa-star" id="s2" data-num="2"></i></span>
                        <span name="score-star"><i class="fas fa-star" id="s3" data-num="3"></i></span>
                        <span name="score-star"><i class="fas fa-star" id="s4" data-num="4"></i></span>
                        <span name="score-star"><i class="fas fa-star" id="s5" data-num="5"></i></span>
                    </span>
                </div>
                <div class="col-sm-2 col-md-2 col-lg-2">
                    <div class="pretty p-default p-round p-smooth p-bigger" style="margin-top:25px;">
                        <input id="include_unrated" type="checkbox" checked/>
                        <div class="state p-primary">
                            <label>Include Unrated Properties</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row col-sm-12 col-md-12 col-lg-12" style="margin-bottom:5px;">
                <div class="col-sm-2 col-md-2 col-lg-2 input-container">
                    <i class="fas fa-bed input-icon"></i>
                    <input id="prop_beds" class="form-control input-field" min="1" type="number">
                </div>
                <div class="col-sm-2 col-md-2 col-lg-2 input-container">
                    <i class="fas fa-bath input-icon"></i>
                    <input id="prop_baths" class="form-control input-field" min="1" type="number">
                </div>
                <div class="col-sm-2 col-md-2 col-lg-2 input-container">
                    <i class="fas fa-car input-icon"></i>
                    <input id="prop_cars" class="form-control input-field" min="1" type="number">
                </div>
            </div>
            <hr>
            <h4 style="margin-left:15px;">Booking Dates</h4>
            <div class="row col-sm-12 col-md-12 col-lg-12 listing_dates">
                <div class="col-sm-3 col-md-3 col-lg-3">
                    <span>Start Date:&nbsp;</span>
                    <input class="form-control" id="start_date" name="start_date" type="text" required>
                </div>
                <div class="col-sm-3 col-md-3 col-lg-3">
                    <span>End Date:&nbsp;</span>
                    <input class="form-control" id="end_date" name="end_date" type="text" required>
                </div>
            </div>
            <hr>
            <div class="row col-sm-12 col-md-12 col-lg-12" style="margin-bottom:0px;">
                <div class="col-sm-6 col-md-6 float-right">
                    <span class="btn btn-xs btn-primary" id="search_props">Search</span>
                </div>
            </div>              
        </div>
    </div>

    <div id="prop_div">
      @foreach ($properties as $p)
        <div class="row card item-card cursor-pointer" name="view_property" data-id="{{$p->property_id}}" style="margin:0px; border:none;">
            <div class="col-sm-12 col-md-12 col-lg-12 card-body" >
                <div class="card-title">
                  <h3>{{ $p->property_title }}</h3>
                </div>
                <div class="card-text">
                  <div style="margin:5px;">
                    <span><i class="fas fa-bed"></i>&nbsp;{{ $p->property_beds }} </span>
                    <span><i class="fas fa-bath"></i>&nbsp;{{ $p->property_baths }} </span>
                    <span><i class="fas fa-car"></i>&nbsp;{{ $p->property_cars }} </span>
                  </div>
                  <div>{{ $p->property_address }}</div>
                  <div style="margin:5px;"> {{ $p->property_desc }}  </div>
                  <div>
                      @foreach($p->tags as $t)
                          <span class="badge badge-secondary">{{$t}}</span>
                      @endforeach
                  </div>
                  <div><i class="fas fa-star @if($p->scores > 2.5 && $p->scores != 'No Reviews Yet') gold-star @endif"></i>&nbsp;{{$p->scores}}@if($p->scores != "No Reviews Yet")({{$p->review_count}} Review(s))@endif</div>
                </div>
            </div>
        </div>
        @can('delete properties')
            <div>
                <label id="delete_property" data-id="{{$p->property_id}}" class="btn btn-primary">✖ Delete Property</label>
            </div>
        @endcan
      @endforeach
    </div>
</div>
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
$(document).ready(function() {

    $('#start_date').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true
    });


    $('#end_date').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,

    });

    $(document).on('change', '[name="start_date"]', function() {
        var date = $(this).val();
        date = date.split('/');
        date = date[2]+'-'+date[1]+'-'+date[0];
        date = new Date(date);
        $(this).closest('.listing_dates').find('[name="end_date"]').datepicker("setStartDate",  date);
    });

    $(document).on('change', '[name="end_date"]', function() {
        var date = $(this).val();
        date = date.split('/');
        date = date[2]+'-'+date[1]+'-'+date[0];
        date = new Date(date);
        $(this).closest('.listing_dates').find('[name="start_date"]').datepicker("setEndDate",  date);
    });

    $(document).on('mouseover', '.fa-star', function() {
        num = $(this).data("num");

        if(num == 1) {
            $('#s1').addClass('gold-star-temp');
        }

        if(num == 2) {
            $('#s1').addClass('gold-star-temp');
            $('#s2').addClass('gold-star-temp');
        }

        if(num == 3) {
            $('#s1').addClass('gold-star-temp');
            $('#s2').addClass('gold-star-temp');
            $('#s3').addClass('gold-star-temp');
        }

        if(num == 4) {
            $('#s1').addClass('gold-star-temp');
            $('#s2').addClass('gold-star-temp');
            $('#s3').addClass('gold-star-temp');
            $('#s4').addClass('gold-star-temp');
        }

        if(num == 5) {
            $('#s1').addClass('gold-star-temp');
            $('#s2').addClass('gold-star-temp');
            $('#s3').addClass('gold-star-temp');
            $('#s4').addClass('gold-star-temp');
            $('#s5').addClass('gold-star-temp');
        }
    });

    $(document).on('mouseout', '.fa-star', function() {
        $('#s1').removeClass('gold-star-temp');
        $('#s2').removeClass('gold-star-temp');
        $('#s3').removeClass('gold-star-temp');
        $('#s4').removeClass('gold-star-temp');
        $('#s5').removeClass('gold-star-temp');

    });

    $(document).on('click', '[name="score-star"]', function() {
        num = $(this).find("i").data("num");

        $('#s1').removeClass('gold-star');
        $('#s2').removeClass('gold-star');
        $('#s3').removeClass('gold-star');
        $('#s4').removeClass('gold-star');
        $('#s5').removeClass('gold-star');

        if(num == 1) {
            $('#s1').addClass('gold-star');
        }

        if(num == 2) {
            $('#s1').addClass('gold-star');
            $('#s2').addClass('gold-star');
        }

        if(num == 3) {
            $('#s1').addClass('gold-star');
            $('#s2').addClass('gold-star');
            $('#s3').addClass('gold-star');
        }

        if(num == 4) {
            $('#s1').addClass('gold-star');
            $('#s2').addClass('gold-star');
            $('#s3').addClass('gold-star');
            $('#s4').addClass('gold-star');
        }

        if(num == 5) {
            $('#s1').addClass('gold-star');
            $('#s2').addClass('gold-star');
            $('#s3').addClass('gold-star');
            $('#s4').addClass('gold-star');
            $('#s5').addClass('gold-star');
        }
        star_score = num;
    });

    var star_score = 1;



    $('#tags').select2({
        theme: "bootstrap",
        placeholder: "E.g. WiFi"
    });

    $('#prop_suburb').select2({
        theme: "bootstrap",
        placeholder: "E.g. Terrigal"
    });

    $(document).on('click', '[name="view_property"]', function() {
        window.location.href = '/view_property/'+$(this).data('id');
    });
    $(document).on('click', '#delete_property', function() {
        var propertyID = $(this).data('id');
        $.ajax({
            url: '/delete_property',
            method: 'POST',
            dataType: 'JSON',
            data: 'propertyID='+propertyID,
            success: function(html) {
                if(html['status'] == "success") {
                    swal({
                        title:"Success!",
                        text: "You have successfully deleted your property.",
                        type:"success",
                    }).then(function(){
                        window.location.href = "/";
                    });
                } else {
                    Swal.fire("Error", "There was an error, please try again!", "error");
                }
            },
            error: function ( xhr, errorType, exception ) {
                var errorMessage = exception || xhr.statusText;
                Swal.fire("Error", "There was a connectivity problem. Please try again.", "error");
            }
        });
    });

    $(document).on('click', '#search_props', function(e) {
        e.preventDefault();

        var rating = star_score;
        var name = $('#prop_name').val();
        var address = $('#prop_address').val();
        var suburbs = $('#prop_suburb').val();
        var tags = $('#tags').val();
        var beds = $('#prop_beds').val();
        var baths = $('#prop_baths').val();
        var cars = $('#prop_cars').val();
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        var include_unrated = $('#include_unrated').prop('checked');

        $.ajax({
            url: '/property_search',
            method: 'POST',
            dataType: 'JSON',
            data: 'rating='+rating+'&name='+name+'&address='+address+'&suburbs='+suburbs+'&tags='+tags+'&beds='+beds+'&baths='+baths+'&cars='+cars+'&start_date='+start_date+'&end_date='+end_date+'&include_unrated='+include_unrated,
            success: function(html) {
                if(html['status'] == 'success') {
                    data = html['data'];
                    $('#prop_div').empty();
                    $('#prop_div').append(data);
                } else {
                    swal('No Results', 'No properties matched your search criteria', 'warning');
                }

            },
            error: function ( xhr, errorType, exception ) {
                var errorMessage = exception || xhr.statusText;
                Swal.fire("Error", "There was a connectivity problem. Please try again.", "error");
            }
        });
    })
});
</script>
@endsection
