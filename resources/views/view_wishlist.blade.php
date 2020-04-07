@extends('layouts.app')

@section('content')
<div class="container">
    @foreach ($wishlist as $w)
      <hr>
      <div class="row">
          <div class="col-sm-12 col-md-12 col-lg-12">
              <h3><b> {{ $w->wishlist_propertyTitle }}</b></h3>
              <div> {{ $w->wishlist_propertyAddress }}  </div>
              <a class="btn btn-primary" name="view_property" href="/view_property/{{$w->wishlist_propertyID}}"> View property </a>
            <!--  <a id="delete_wishlist <?php echo $w->wishlist_propertyID ?>" class="btn btn-primary">✖</a> -->
              <input class ="delete_wishlist" type="button" id="<?php echo $w->wishlist_propertyID?>", value = "✖" </input>
          </div>
      </div>
    @endforeach

</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '.delete_wishlist', function(e) {
        e.preventDefault();
        var propertyID = $(this).get(0).id;
        alert(propertyID);
        alert("Removed this item from your wishlist!");
        $.ajax({
            url: '/delete_wishlist',
            method: 'POST',
            dataType: 'JSON',
            data: 'propertyID='+propertyID,
            success: function(html) {
                var data = JSON.parse(html);
                if(data['status'] == "success") {
                    alert("Success!");
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
