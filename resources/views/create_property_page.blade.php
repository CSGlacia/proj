@extends('layouts.app')

@section('style')
<style>
#image_drop {
    background: white;
    border-radius: 5px;
    border: 2px dashed black;
    border-image: none;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}
</style>
@endsection

@section('content')
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
            <hr>
            <h5>Property Images:&nbsp;</h5>
            <div class="row">
                <div id="image_drop" class="dropzone"></div>
            </div>
            <hr>
            <h5>Listing Dates:&nbsp;</h5>
            <span id="dates_start">
                <div class="row listing_dates" name="listing_dates">
                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <span>Start Date:&nbsp;</span>
                        <input class="form-control" name="start_date" type="date" required>
                    </div>
                    <div class="col-sm-3 col-md-3 col-lg-3">
                        <span>End Date:&nbsp;</span>
                        <input class="form-control" name="end_date" type="date" required>
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
            <div class="pretty p-default p-round p-smooth p-bigger" style="margin-bottom:20px;">
                <input id="always_list" type="checkbox" />
                <div class="state p-primary">
                    <label>List my property indefinitely</label>
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

<!-- code for the preview template for file uploads taken from https://codepen.io/blackjacques/pen/jyxNqL -->
<DIV id="preview-template" style="display: none;">
<DIV class="dz-preview dz-file-preview">
<DIV class="dz-image"><IMG data-dz-thumbnail=""></DIV>
<DIV class="dz-details">
<DIV class="dz-size"><SPAN data-dz-size=""></SPAN></DIV>
<DIV class="dz-filename"><SPAN data-dz-name=""></SPAN></DIV></DIV>

<DIV class="dz-error-message"><SPAN data-dz-errormessage=""></SPAN></DIV>
<div class="dz-success-mark">
  <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
    <title>Check</title>
    <desc>Created with Sketch.</desc>
    <defs></defs>
    <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">
        <path d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" stroke-opacity="0.198794158" stroke="#747474" fill-opacity="0.816519475" fill="#FFFFFF" sketch:type="MSShapeGroup"></path>
    </g>
  </svg>
</div>
<div class="dz-error-mark">
  <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns">
      <title>error</title>
      <desc>Created with Sketch.</desc>
      <defs></defs>
      <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd" sketch:type="MSPage">
          <g id="Check-+-Oval-2" sketch:type="MSLayerGroup" stroke="#747474" stroke-opacity="0.198794158" fill="#FFFFFF" fill-opacity="0.816519475">
              <path d="M32.6568542,29 L38.3106978,23.3461564 C39.8771021,21.7797521 39.8758057,19.2483887 38.3137085,17.6862915 C36.7547899,16.1273729 34.2176035,16.1255422 32.6538436,17.6893022 L27,23.3431458 L21.3461564,17.6893022 C19.7823965,16.1255422 17.2452101,16.1273729 15.6862915,17.6862915 C14.1241943,19.2483887 14.1228979,21.7797521 15.6893022,23.3461564 L21.3431458,29 L15.6893022,34.6538436 C14.1228979,36.2202479 14.1241943,38.7516113 15.6862915,40.3137085 C17.2452101,41.8726271 19.7823965,41.8744578 21.3461564,40.3106978 L27,34.6568542 L32.6538436,40.3106978 C34.2176035,41.8744578 36.7547899,41.8726271 38.3137085,40.3137085 C39.8758057,38.7516113 39.8771021,36.2202479 38.3106978,34.6538436 L32.6568542,29 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z" id="Oval-2" sketch:type="MSShapeGroup"></path>
          </g>
      </g>
  </svg>
</div>

@endsection

@section('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBPDGVcOB-lSlE4tKgMpQwUbz_2d55B6xE&libraries=places&callback=initMap"
        async defer></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.0/min/dropzone.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.0/dropzone.js"></script>
<script>
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

Dropzone.autoDiscover = false;

$(document).ready(function() {

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
            $('#dates_start').append('<div class="row listing_dates" name="listing_dates"><div class="col-sm-3 col-md-3 col-lg-3"><span>Start Date:&nbsp;</span><input class="form-control" name="start_date" type="date" required></div><div class="col-sm-3 col-md-3 col-lg-3"><span>End Date:&nbsp;</span><input class="form-control" name="end_date" type="date" required></div><div class="col-sm-1 col-md-1 col-lg-1" style="margin-top:27px;"><div class="pretty p-default p-round p-smooth p-bigger"><input name="reccur_dates" type="checkbox" /><div class="state p-primary"><label>Set as reccuring dates</label></div></div></div><div class="col-sm-1 col-md-1 col-lg-1" style="margin-left:100px;"><label class="btn btn-danger float-left" name="remove_dates" style="margin-top:22px;"><i class="fas fa-times"></i></label></div></div>');
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

    var fileDrop = new Dropzone('#image_drop', {
        url: '/image_upload',
        autoProcessQueue: false,
        uploadMultiple: true,
        maxFiles: 5,
        parallelUploads: 5,
        sending: function(file, xhr, formData) {
            formData.append("_token", "{{ csrf_token() }}");
        },
        addRemoveLinks: true,
        previewTemplate: $('#preview-template').html(),
        dictDefaultMessage: '<div style="text-align:center;margin:10px;"><i class="fas fa-file-upload fa-7x"></i></div><div>Click here or drag and drop photos of your property to upload them</div>'
    });

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
        var always_list = $('#always_list').prop('checked');

        //var image_name = "image";
        var form_data = new FormData();
        /*
        var no_images = $('#property_images')[0].files.length;
        for (var i = 0; i < no_images; i++){
            var images = $('#property_images').prop('files')[i];
            form_data.append('files[]',images,image_name.concat(i.toString(10)));
        }
        */
        form_data.append('address',address);
        form_data.append('beds',beds);
        form_data.append('baths',baths);
        form_data.append('cars',cars);
        form_data.append('desc',desc);
        form_data.append('l_name',l_name);
        form_data.append('lat',lat);
        form_data.append('lng',lng);
        form_data.append('always_list',always_list);

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
                    fileDrop.options.url = "/upload_property_images/"+html['id']
                    fileDrop.processQueue();
                    var prop_id = html['id'];

                    if(always_list == false) {
                        $.each(listing_dates_arr, function(i) {
                            $.ajax({
                                url: '/create_property_listing',
                                method: 'POST',
                                dataType: 'JSON',
                                data: 'property='+prop_id+'&price='+1+'&start_date='+listing_dates_arr[i][0]+'&end_date='+listing_dates_arr[i][1]+'&recurr='+listing_dates_arr[i][2],
                                success: function(html) {
                                    if(html['status'] == "success") {
                                        Swal.fire("Success", "Property Listing created successfully", "success");
                                    } else if(html['status'] == 'bad_input'){
                                        Swal.fire("Warning", "Please check all fields are filled.", "warning");
                                    } else if(html['status'] == 'price_low') {
                                        Swal.fire("Warning", "You must enter a price which is positive. You cannot charge negative amounts.", "warning");
                                    } else if(html['status'] == 'price_high'){
                                        Swal.fire("Warning", "There's a price limit of $999999.99 . Please enter a lower price per night.", "warning");
                                    } else if(html['status'] == 'date_invalid'){
                                        Swal.fire("Warning", "Your start date must be before your end date and today or after.", "warning")
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
                    }
                    Swal.fire("Success", "Property Created Successfully", "success");

                } else if(html['status'] == 'bad_input') {
                    Swal.fire("Warning", "Please double check all fields are filled!", "warning");
                } else if(html['status'] == 'wrong_state') {
                    Swal.fire("Warning", "Please ensure your property is in NSW", "warning");
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
});


</script>
@endsection
