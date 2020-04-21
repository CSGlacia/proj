@extends('layouts.app')

@section('content')
<div class="container" style="background:rgba(240,255,248,0.6)">
    <h1>Your Wishlist</h1>
    <hr>
    @foreach ($wishlist as $w)
      <div class="row card card-text" style="margin-bottom:5px">
          <div class="col-sm-12 col-md-12 col-lg-12"  style="margin-bottom:10px">
              <h3><b> {{ $w->wishlist_propertyTitle }}</b></h3>
              <div> {{ $w->wishlist_propertyAddress }}  </div>
              <a class="btn btn-primary" name="view_property" href="/view_property/{{$w->wishlist_propertyID}}"> View property </a>
            <!--  <a id="delete_wishlist <?php echo $w->wishlist_propertyID ?>" class="btn btn-primary">✖</a> -->
              <a class ="delete_wishlist btn btn-danger" style="color:white"type="" id="<?php echo $w->wishlist_propertyID?>", <>Remove from wishlist</a>
          </div>
      </div>
    @endforeach
    <br>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '.delete_wishlist', function(e) {
        e.preventDefault();
        var propertyID = $(this).get(0).id;

        $.ajax({
            url: '/delete_wishlist',
            method: 'POST',
            dataType: 'JSON',
            data: 'propertyID='+propertyID,
            success: function(html) {
                if(html['status'] == "success") {
                    let timerInterval
                    Swal.fire({
                    title: 'Property has been removed',
                    html: 'You will be redirected in <b></b> seconds.',
                    timer: 5000,
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
                        location.reload()
                    }
                    }).then((result) => {
                        location.reload()
                    })
                } else {
                    Swal.fire("Error", "There was an error, please try again!", "error");
                }
            },
            error: function ( xhr, errorType, exception ) {
                var errorMessage = exception || xhr.statusText;
                Swal.fire("Error", "There was a connectivity problem. Please try again.", "error");
            }
        });
      location.reload(true);
    });
});
</script>
@endsection
