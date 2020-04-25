@extends('layouts.app')

@section('content')
<link href="{{asset('css/create_property_listing.css')}}" rel="stylesheet">
<div class="container">
    <div class="card col-sm-12 col-md-12 col-lg-12" id="top" style="background:rgba(240,255,248,0.6)">
        <br>
        <b><h3 style="text-align:center;">Create a New Property Listing</h3></b>
        <hr>
        <div id="listing_form" class="form-group">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6" >
                    <label for="form_select_property">Choose the property you want to list:</label>
                        <select class="form-control" id="form_select_property">
                        @foreach($properties as $p)
                        <option value={{$p->property_id}}>{{$p->property_title}}</option>
                        @endforeach
                        </select>
                </div>
            </div>
            <h5>Listing Dates:&nbsp;</h5>
            <span id="dates_start">
                <div class="row listing_dates" name="listing_dates">
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <span>Start Date:&nbsp;</span>
                        <input class="form-control" id="first_start_date" name="start_date" type="text" required>
                    </div>
                    <div class="col-sm-2 col-md-2 col-lg-2">
                        <span>End Date:&nbsp;</span>
                        <input class="form-control" id="first_end_date" name="end_date" type="text" required>
                    </div>
                    <div class="col-sm-1 col-md-1 col-lg-1" style="margin-top:27px;">
                        <div class="pretty p-default p-round p-smooth p-bigger">
                            <input name="reccur_dates" type="checkbox" />
                            <div class="state p-primary">
                                <label>Set as reccuring dates</label>
                            </div>
                        </div>
                    </div>
                </div>
            </span>
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6" name="listing_dates">
                    <label class="btn btn-primary float-left" id="add_dates"><i class="fas fa-plus"></i>&nbsp;Add Dates</label>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12" align="center">
                    <a id="property_list_submit" style="color:white;text-align:center ;color:white;width:20vw;height:6vh;margin-top:20px" class="btn btn-primary">Submit</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('#first_end_date').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true
    });


    $('#first_start_date').datepicker({
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

    var count = 1;

    $(document).on('click', '#add_dates', function(e) {
        if(count < 5) {
            var elem = $('#dates_start').append('<div class="row listing_dates" name="listing_dates"><div class="col-sm-2 col-md-2 col-lg-2"><span>Start Date:&nbsp;</span><input class="form-control" name="start_date" type="text" required></div><div class="col-sm-2 col-md-2 col-lg-2"><span>End Date:&nbsp;</span><input class="form-control" name="end_date" type="text" required></div><div class="col-sm-1 col-md-1 col-lg-1" style="margin-top:27px;"><div class="pretty p-default p-round p-smooth p-bigger"><input name="reccur_dates" type="checkbox" /><div class="state p-primary"><label>Set as reccuring dates</label></div></div></div><div class="col-sm-1 col-md-1 col-lg-1" style="margin-left:100px;"><label class="btn btn-danger float-left" name="remove_dates" style="margin-top:22px;"><i class="fas fa-times"></i></label></div></div>');


            $('[name="start_date"]').each(function() {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true
                });
            });

            $('[name="end_date"]').each(function() {
                $(this).datepicker({
                    format: 'dd/mm/yyyy',
                    autoclose: true
                });
            });

            count++;
        } else {
            Swal.fire("Warning", "You can only have 5 listing periods maximum", "warning");
        }
    });

    $(document).on('click', '[name="remove_dates"]', function(e) {
        e.preventDefault();
        $(this).parents(".row").remove();
        count--;
    });
});

$(document).ready(function() {

    $(document).on('click', '#property_list_submit', function(e) {
        e.preventDefault();
        var listing_dates_arr = [];
        $('.listing_dates').each(function (i) {
                var start_date = $(this).find('input[name="start_date"]').val();
                var end_date = $(this).find('input[name="end_date"]').val();
                var recur = $(this).find('input[name="reccur_dates"]').prop('checked');
                var add_arr = [];

                add_arr.push(start_date);
                add_arr.push(end_date);
                add_arr.push(recur);
                listing_dates_arr.push(add_arr);
        });
        var property = $('#form_select_property').val();
        var count = 1;
        $.each(listing_dates_arr, function(i) {
            $.ajax({
                url: '/create_property_listing',
                method: 'POST',
                dataType: 'JSON',
                data: 'property='+property+'&start_date='+listing_dates_arr[i][0]+'&end_date='+listing_dates_arr[i][1]+'&recurr='+listing_dates_arr[i][2],
                success: function(html) {
                    if(html['status'] == "success") {
                        $('<div class="alert alert-success" role="alert">Listing '+ count +': was created successfully' +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">' +
                        '&times; </span></button></div>').hide().appendTo('#top').fadeIn(1000);
                    } else if(html['status'] == 'bad_input'){
                        $('<div class="alert alert-danger" role="alert">Listing '+ count +': Please check all fields are filled.' +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">' +
                        '&times; </span></button></div>').hide().appendTo('#top').fadeIn(1000);
                    } else if(html['status'] == 'overlapping_date'){
                        $('<div class="alert alert-danger" role="alert">Listing '+ count +': A listing already exists within this time period.' +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">' +
                        '&times; </span></button></div>').hide().appendTo('#top').fadeIn(1000);
                    } else {
                        $('<div class="alert alert-danger" role="alert">Listing '+ count +': There was an error with creating your listing!' +
                        '<button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">' +
                        '&times; </span></button></div>').hide().appendTo('#top').fadeIn(1000);
                    }
                    count++;
                },
                error: function ( xhr, errorType, exception ) {
                    var errorMessage = exception || xhr.statusText;
                    Swal.fire("Error", "There was a connectivity problem. Please try again.", "error");
                }
            });
        });

    });
});

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}
</script>
@endsection
