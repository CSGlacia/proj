@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card col-sm-12 col-md-12 col-lg-12">
        <br>
        <b><h3 style="text-align:center;">Create a New Property Listing</h3></b>
        <hr>
        <div id="listing_form" class="form-group">
            <h5>Location Details:&nbsp;</h5>
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <span>Address:&nbsp;</span>
                    <input id="address" class="form-control" type="text" placeholder="E.g. 6/5 George Street" required>
                </div>
            </div>
            <hr>
            <h5>Property Details:&nbsp;</h5>
            <div class="row">
                <div class="col-sm-4 col-md-4 col-lg-4">
                    <span># of Bedrooms:&nbsp;</span>
                    <input id="beds" class="form-control" type="number" placeholder="0" required>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4">
                    <span># of Bathrooms:&nbsp;</span>
                    <input id="baths" class="form-control" type="number" placeholder="0" required>
                </div>
                <div class="col-sm-4 col-md-4 col-lg-4">
                    <span># of Car Spaces:&nbsp;</span>
                    <input id="cars" class="form-control" type="number" placeholder="0" required>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <textarea id="property_desc" class="form-control" rows="5" placeholder="Please enter a brief description of the property." required></textarea>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <a id="property_submit" class="btn btn-primary">Submit</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '#property_submit', function(e) {
        e.preventDefault();
        var address = $('#address').val();
        var beds  = $('#beds').val();
        var baths = $('#baths').val();
        var cars = $('#cars').val();
        var desc = $('#property_desc').val();

        $.ajax({
            url: '/create_property',
            method: 'POST',
            data: 'address='+address+'&beds='+beds+'&baths='+baths+'&cars='+cars+'&desc='+desc,
            success: function(html) {
                var data = tryParseJSON(html);

                if(data['status'] == "success") {
                    alert("Property Created Successfully");
                } else if(data['status'] == 'bad_input') {
                    alert("Please double check all fields are filled!");
                } else {
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
