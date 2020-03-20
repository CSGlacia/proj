@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card col-sm-12 col-md-12 col-lg-12">
        <br>
        <b><h3 style="text-align:center;">Book a property</h3></b>
        <hr>
        <div id="listing_form" class="form-group">
            <h5>Booking Details:&nbsp;</h5>
            <div class="row">
                <div class="col-sm-3 col-md-3 col-lg-3">
                    <span>(!) Property ID:&nbsp;</span>
                    <input id="propertyID" class="form-control" type="number" placeholder="(int)" required>
                </div>
                <div class="col-sm-3 col-md-3 col-lg-3">
                    <span>Start Date:&nbsp;</span>
                    <input id="startDate" class="form-control" type="date" placeholder="(dateTime)" required>
                </div>
                <div class="col-sm-3 col-md-3 col-lg-3">
                    <span>End Date:&nbsp;</span>
                    <input id="endDate" class="form-control" type="date" placeholder="(dateTime)" required>
                </div>
                <div class="col-sm-3 col-md-3 col-lg-3">
                    <span>(!) Number of People:&nbsp;</span>
                    <input id="persons" class="form-control" type="number" placeholder="(int)" required>
                </div>
            </div>
            <hr>
            <h5>(!) Administration:&nbsp;</h5>
            <div class="row">
                <div class="col-sm-1 col-md-1 col-lg-1">
                    <span>Paid?:&nbsp;</span>
                    <input id="paid" class="form-control" type="number" value="0" required>
                </div>
                <div class="col-sm-3 col-md-3 col-lg-3">
                    <span>Status? OK / INACTIVE / (?):&nbsp;</span>
                    <input id="status" class="form-control" type="text" value="OK" required>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <a id="submit" class="btn btn-primary">Submit</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '#submit', function(e) {
        e.preventDefault();

        var propertyID = $('#propertyID').val();
        var startDate = $('#startDate').val();
        var endDate = $('#endDate').val();
        var persons = $('#persons').val();
        var paid = $('#paid').val();
        var status = $('#status').val();

        $.ajax({
            url: '/create_booking',
            method: 'POST',
            data: 'propertyID='+propertyID+'&startDate='+startDate+'&endDate='+endDate+'&persons='+persons+'&paid='+paid+'&status='+status,
            success: function(html) {
                var data = JSON.parse(html);

                if(data['status'] == "success") {
                    alert("Success!");
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
