@extends('layouts.app')

@section('content')
<div class="container">
    <span class="label bg-primary">Name: {{$user->name}}</span>
    <span class="label bg-warning">Email: {{$user->email}}</span>

    <div class="row">
        <b><h2>Bookings:</h2></b>
        <div class="col-sm-12 col-md-12 col-lg-12">
            @foreach($bookings as $b) 
                <div>{{$b}}</div>
            @endforeach
        </div>
    </div>
    <div class="row">
        <b><h2>Properties:</h2></b>
        <div class="col-sm-12 col-md-12 col-lg-12">
            @foreach($properties as $p) 
                {{$p}}
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {

});
</script>
@endsection
