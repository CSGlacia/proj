@extends('layouts.app')

@section('content')
<div class=container style="background:rgba(240,255,248,0.6)">
    <h1>All Users</h1>
    <hr>
    <div class="container row" style="margin:auto">
        @foreach ($users as $u)
          <hr>
          <div class="row" align="center">
              <div class="col-sm-9 col-md-9 col-lg-9 card" style="margin:0px; width:20vw;">
              <h5>Username: {{$u->name}}</h5>
                <h6> ID: {{$u->id}}</h6>
                @if ($advertisers[$u->id] == False)
                    <a class="btn btn-primary"  style="margin:5px; color:white" name="change_user" data-id="{{$u->id}}"> Make Advertiser</a>
                @else
                    <a class="btn btn-primary"  style="margin:5px; color:white" name="change_user" data-id="{{$u->id}}"> Remove Advertiser status</a>
                @endif
                    
              </div>
          </div>
        @endforeach

    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
        var user_id;

        $(document).on('click', '[name="change_user"]', function(){

            user_id = $(this).data('id');
            $.ajax({
                url: '/admin_advertiser',
                method: 'POST',
                data: 'user_id='+user_id,
                success: function(html) {
                    var data = JSON.parse(html);
                    if(data['status'] == "success") {
                        Swal.fire("Success", "Role Changed Successfully", "success");
                    } else {
                        Swal.fire("Error", "There was an error, please try again!", "error");
                    }
                    location.reload();
                },
            });
        });
});
</script>
@endsection
