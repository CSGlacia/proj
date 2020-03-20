<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Turtle</title>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">    

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
</head>

<style>
    .topnav {
      background-color: #333;
      overflow: hidden;
    }
    .card {
        margin: 10px;
    }
    .row {
        margin-bottom: 20px;
    }
</style>

<body>
    <div class="topnav">
        @if (Route::has('login'))
            <div class="top-right links">
                    <a href="/">Homepage</a>
                @auth
                    <a id="user_profile" href="">User Profile</a>
                    <a href="{{ url('/logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a>
                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                    <a href="/create_listing">Create a property listing</a>
                @else
                    <a href="{{ route('login') }}">Login</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}">Register</a>
                    @endif
                @endauth
            </div>
        @endif
    </div>

    <main class="py-4">
        @yield('content')
    </main>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


            function set_profile_button() {
                $.ajax({
                    url: '/get_user_id',
                    method: 'GET',
                    success: function(html) {
                        var data = JSON.parse(html);
                        if(data['status'] == "success") {
                            var id = data['id'];
                            $('#user_profile').attr('href', '/user_profile/'+id);
                        } else {
                            alert("There was an error, please try again!");
                        }
                    },
                    error: function ( xhr, errorType, exception ) {
                        var errorMessage = exception || xhr.statusText;
                        alert("There was a connectivity problem. Please try again.");
                    }
                });
            }
        $(document).ready(function() {

            set_profile_button();
        });
    </script>
    @yield('scripts')
</body>



</html>
