@extends('layouts.app')

@section('content')
<div class="container" style="background:rgba(240,255,248,0.6)">
    <h2><b>Booking</b></h2>
    <hr>
    <div class="row" style="background:rgba(255,255,255,0.6); margin:5px">
        <div class="col-sm-6 col-md-6 col-lg-6">
            <h3><b>Property Details</b></h3>
            <div><i class="fas fa-home"></i>&nbsp;<a href="/view_property/{{$b->property_id}}">{{$b->property_title}}</a></div>
            <div>{{$b->property_address}}</div>
            <span><i class="fas fa-bed"></i> {{$b->property_beds}}</span>&nbsp;<span><i class="fas fa-bath"></i> {{$b->property_baths}}</span>&nbsp;<span><i class="fas fa-car"></i> {{$b->property_cars}}</span>
            <div>Description: {{$b->property_desc}}</div>
            <div>Owner: <a href="/user_profile/{{$b->property_user_id}}">{{$prop_owner_name->name}}</a></div>
            @if($b->scores != -1)
            <div ><i class="fas fa-star @if($b->scores > 2.5) gold-star @endif"></i>{{$b->scores}}</div>
            @endif
        </div>
        <div class="col-sm-6 col-md-6 col-lg-6">
            <h3><b>Booking Details</b></h3>
            <div>Tennant:&nbsp;<a href="/user_profile/{{$b->id}}">{{$b->name}}</a></div>
            <div>Start Date: {{$b->booking_startDate}}</div>
            <div>End Date: {{$b->booking_endDate}}</div>
            <div>Persons Booked: {{$b->booking_persons}}</div>
            <div>Status: {{$status}}</div>
        </div>
    </div>
    <div class="row" style="text-align:center; margin:10px;">
        <div class="col-sm-4 col-md-4 col-lg-4">
            
        </div>
        <div class="col-sm-4 col-md-4 col-lg-4">        
            <span><h3><b>Cost of Stay: ${{$b->booking_price}}</b></h3></span>
        </div>
        <div class="col-sm-4 col-md-4 col-lg-4">
            
        </div>
    </div>
    @if($user_id == $b->property_user_id && $status == 'NOT APPROVED')
        <div class="row" style="margin-top:5px;"><span class="btn btn-success" name="approve_booking" data-id="{{$b->booking_id}}">Approve Booking</span>&nbsp;<span class="btn btn-warning" name="deny_booking" data-id="{{$b->booking_id}}">Deny Booking</span></div>
    @endif
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

    $(document).on('click', '[name="approve_booking"]', function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: '/approve_booking/'+id,
            method: 'GET',
            dataType: 'JSON',
            success: function(html) {
                if(html['status'] == "success") {
                    let timerInterval
                            Swal.fire({
                            title: 'Booking approved successfully',
                            html: 'You will be redirected in <b></b> seconds.',
                            timer: 3000,
                            timerProgressBar: true,
                            type: "success",
                            onBeforeOpen: () => {
                                Swal.showLoading()
                                timerInterval = setInterval(() => {
                                    swal.getContent().querySelector('b')
                                    .textContent = Math.ceil(swal.getTimerLeft() / 1000)
                                }, 100)
                            },
                            onClose: () => {
                                location.reload();
                            }
                            }).then((result) => {
                                location.reload();
                            })
                } else if(html['status'] == 'overlapping_bookings') {
                    Swal.fire("Warning", "An approved booking overlaps with this booking. This booking has been denied", "warning");
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

    $(document).on('click', '[name="deny_booking"]', function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: '/deny_booking/'+id,
            method: 'GET',
            dataType: 'JSON',
            success: function(html) {
                console.log(html);
                if(html['status'] == "success") {
                    swal({
                        title:"Success!",
                        text: "Booking denied successfully",
                        type:"success",
                    });
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
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
