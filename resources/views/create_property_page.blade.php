@extends('layouts.app')

@section('style')
<style>
</style>
@endsection

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
                    <input type="hidden" id="lat" name="lat" />
                    <input type="hidden" id="lng" name="lng" />  
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
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBPDGVcOB-lSlE4tKgMpQwUbz_2d55B6xE&libraries=places&callback=initMap"
        async defer></script>

<script>
    // This example requires the Places library. Include the libraries=places
    // parameter when you first load the API. For example:
    // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">

    function initMap() {
        var input = document.getElementById('address');
        var autocomplete = new google.maps.places.Autocomplete(input);
    
        // Set the data fields to return when the user selects a place.
        autocomplete.setFields(
            ['address_components', 'geometry', 'name']);
        autocomplete.setComponentRestrictions({'country':'au'});
        autocomplete.setTypes(['address']);
        autocomplete.setBounds({'north': -28.156990, 'south': -37.504447, 'east': 153.6384121, 'west': 140.999265});
        google.maps.event.addListener(autocomplete, 'place_changed', function () {
            var place = autocomplete.getPlace();

            if(!place.geometry) {
                window.alert("No details available for this address");
                return
            }
            var place_name = place.name;
            document.getElementById('lat').value = place.geometry.location.lat();
            document.getElementById('lng').value = place.geometry.location.lng();
        });
    }

$(document).ready(function() {
    $(document).on('click', '#property_submit', function(e) {
        e.preventDefault();

        var address = $('#address').val();
        var beds  = $('#beds').val();
        var baths = $('#baths').val();
        var cars = $('#cars').val();
        var desc = $('#property_desc').val();
        var l_name = $('#l_name').val();
        var lat = $('#lat').val();
        var lng = $('#lng').val();

        var image_name = "image";
        var form_data = new FormData();
        
        var no_images = $('#property_images')[0].files.length;
        for (var i = 0; i < no_images; i++){
            var images = $('#property_images').prop('files')[i];
            form_data.append('files[]',images,image_name.concat(i.toString(10)));
        }
        
        form_data.append('address',address);
        form_data.append('beds',beds);
        form_data.append('baths',baths);
        form_data.append('cars',cars);
        form_data.append('desc',desc);
        form_data.append('l_name',l_name);
        form_data.append('lat',lat);
        form_data.append('lng',lng);
        $.ajax({
            url: '/create_property',
            method: 'POST',
            dataType: 'JSON',
            data: form_data,
            processData: false,
            contentType: false,
            cache: false,
            success: function(html) {
                if(html['status'] == "success") {
                    alert("Property Created Successfully");
                } else if(html['status'] == 'bad_input') {
                    alert("Please double check all fields are filled!");
                } else if(html['status'] == 'wrong_state') {
                    alert("Please ensure your property is in NSW");
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