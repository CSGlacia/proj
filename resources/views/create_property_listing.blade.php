@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card col-sm-12 col-md-12 col-lg-12">
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
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <label for="form_property_price_per_night">How much would you like the property to cost per night?</label>
                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <input id="price" class="form-control" type="number" placeholder="0" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-auto">
                <label for="form_property_list_start_date">Start Date:</label>
                    <input id="form_property_list_start_date" class="form-control" type="date" required>
                </div>
                <div class="col-auto">
                <label for="form_property_list_end_date">End Date:</label>
                    <input id="form_property_list_end_date" class="form-control" type="date" required>
                </div>
            </div>
            <h5>Listing Dates:&nbsp;</h5>
            <span id="dates_start">
                <div class="row listing_dates" name="listing_dates">
                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <span>Start Date:&nbsp;</span>
                        <input class="form-control" id="first_start_date" name="start_date" type="text" required>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3">
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
            <div class="col-sm-6 col-md-6 col-lg-6" name="listing_dates">
                <label class="btn btn-primary float-right" id="add_dates"><i class="fas fa-plus"></i>&nbsp;Add Dates</label>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <a id="property_list_submit" class="btn btn-primary">Submit</a>
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

    $(document).on('change', '#always_list', function(e) {
        e.preventDefault();
        var val = $(this).prop('checked');

        if(val) {
            $('[name="listing_dates"]').each(function(i) {
                $(this).hide();
            });
        } else {
            $('[name="listing_dates"]').each(function(i) {
                $(this).show();
            });
        }
    });

    $(document).on('click', '#add_dates', function(e) {
        if(count < 5) {
            var elem = $('#dates_start').append('<div class="row listing_dates" name="listing_dates"><div class="col-sm-3 col-md-3 col-lg-3"><span>Start Date:&nbsp;</span><input class="form-control" name="start_date" type="text" required></div><div class="col-sm-3 col-md-3 col-lg-3"><span>End Date:&nbsp;</span><input class="form-control" name="end_date" type="text" required></div><div class="col-sm-1 col-md-1 col-lg-1" style="margin-top:27px;"><div class="pretty p-default p-round p-smooth p-bigger"><input name="reccur_dates" type="checkbox" /><div class="state p-primary"><label>Set as reccuring dates</label></div></div></div><div class="col-sm-1 col-md-1 col-lg-1" style="margin-left:100px;"><label class="btn btn-danger float-left" name="remove_dates" style="margin-top:22px;"><i class="fas fa-times"></i></label></div></div>');


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
        var property = $('#form_select_property').val();
        var price = $('#price').val();
        var start_date = $('#form_property_list_start_date').val();
        var end_date = $('#form_property_list_end_date').val();
        $.ajax({
            url: '/create_property_listing',
            method: 'POST',
            dataType: 'JSON',
            data: 'property='+property+'&price='+price+'&start_date='+start_date+'&end_date='+end_date,
            success: function(html) {
                if(html['status'] == "success") {
                    alert("Property Listing created successfully");
                } else if(html['status'] == 'bad_input'){
                    alert("Please check all fields are filled.");
                } else if(html['status'] == 'price_low') {
                    alert("You must enter a price which is positive. You cannot charge negative amounts.");
                } else if(html['status'] == 'price_high'){
                    alert("There's a price limit of $999999.99 . Please enter a lower price per night.");
                } else if(html['status'] == 'date_invalid'){
                    alert("Your start date must be before your end date and today or after.")
                } else if(html['status'] == 'overlapping_date'){
                    alert("A listing already exists within this time period.")
                }
                else {
                    alert("There was an error, please try again!");
                }
            },
            error: function ( xhr, errorType, exception ) {
                var errorMessage = exception || xhr.statusText;
                alert("There was a connectivity problem. Please try again.");
            }
        });
    });
});
</script>
@endsection
