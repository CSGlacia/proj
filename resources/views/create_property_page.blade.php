@extends('layouts.app')

@section('content')
<!-- <script src="js/dropzone.min.js"></script>
<link rel="stylesheet" src="css/dropzone.min.css"> 
https://jsfiddle.net/45qyakg9/-->
<div class="container">
    <div class="card col-sm-12 col-md-12 col-lg-12">
        <br>
        <b><h3 style="text-align:center;">Add a new property to your account</h3></b>
        <hr>
        <div id="listing_form" class="form-group">
            <h5>Location Details:&nbsp;</h5>
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <span>Address:&nbsp;</span>
                    <input id="address" class="form-control" type="text" placeholder="E.g. 6/5 George Street" required>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <span>Suburb:&nbsp;</span>
                    <input id="suburb" class="form-control" type="text" placeholder="E.g. Ryde" required>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <span>Postcode:&nbsp;</span>
                    <input id="postcode" class="form-control" type="number" placeholder="E.g. 2225" required>
                </div>
            </div>
            <hr>
            <h5>Property Details:&nbsp;</h5>
            <div class="row">
                <div class="col-sm-8 col-md-8 col-lg-8">
                    <span>Listing Name:&nbsp;</span>
                    <input id="l_name" class="form-control" type="text" placeholder="E.g. Grand Beachouse" required>
                </div>
            </div>
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
            <div class="row">
                <input id="property_images" type="file" multiple required>
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

        //var address = ;
        var suburb = $('#suburb').val();
        var postcode = $('#postcode').val();
        var beds  = $('#beds').val();
        var baths = $('#baths').val();
        var cars = $('#cars').val();
        var desc = $('#property_desc').val();
        var l_name = $('#l_name').val();
        var images = $('#property_images').prop('files')[0];
        //var num = $('#property_images').files.length;
        var form_data = new FormData();
        form_data.append('files',images,'photo');

        $.ajax({
            url: '/create_property',
            method: 'POST',
            dataType: 'JSON',
            data: form_data,
            processData: false,
            contentType: false,
            cache: false,
            success: function(html) {
                var data = JSON.parse(html);

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