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
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="{{ asset('js/owl.carousel.min.js') }}"></script>
    
    <!-- Sweet Alert -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.css" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.js"></script>

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/pretty-checkbox@3.0/dist/pretty-checkbox.min.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/nav_bar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('css/home.css') }}" rel="stylesheet">
    <link href="{{asset('css/owl.carousel.min.css')}}" rel="stylesheet">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <script src="https://kit.fontawesome.com/06f34836f7.js" crossorigin="anonymous"></script>
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
    .item-card:hover {
        background-color: #f6f6f6;
        box-shadow: inset 0 0 0 5px #85CB33;
        transition: all 1s ease;  
    }
    .item-card {
        background-color: #ffffff;
        width:75%;
    }
    .cursor-pointer:hover{
        cursor: pointer;
    }
    .gold-star {
        color:gold;
    }
    .gold-star-temp {
        color:gold;
    }
    .prop-img {
        position: relative;
        width:100%;
    }

    /* Overlay animtaitons */
    .image-overlay {
      position: absolute;
      top: 0;
      bottom: 0;
      left: 0;
      right: 0;
      height: 100%;
      width: 100%;
      opacity: 0;
      transition: .8s ease;
      background-color: red;
      border-radius: 10px;
    }

    .image-container:hover .image-overlay {
      opacity: 1;
      cursor: pointer;
    }

    .image-icon {
      color: white;
      font-size: 100px;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      -ms-transform: translate(-50%, -50%);
      text-align: center;
    }
    /* End overlay animations
</style>
@yield('style')




<body>
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
    <div class="container">

        <a class="navbar-brand" href="/">TURTLE</a>
            @if (Route::has('login'))
            @auth
                <ul class="navbar-nav">
                    <li class="nav-item"><a href="/create_property_page" class="nav-link">Add Property</a></li>
                    <li class="nav-item"><a href="/property_reviews" class="nav-link">Property Reviews</a></li>
                    <li class="nav-item"><a href="/tennant_reviews" class="nav-link">Tennant Reviews</a></li>



                    <li class="nav-item"><a href="" id="user_profile" class="nav-link">Profile</a></li>
                    <li class="nav-item"><a href="{{ url('/logout') }}" class="nav-link" onclick="event.preventDefault();document.getElementById('logout-form').submit();">Logout</a></li>
                    <form class="btn btn-xs btn-primary" id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </ul>
            @else
                <ul class="navbar-nav">
                    <li class="nav-item"><a href="{{ route('login') }}" class="nav-link">Login</a></li>
                    @if (Route::has('register'))
                        <li class="nav-item"><a href="{{ route('register') }}" class="nav-link">Register</a></li>
                    @endif
                </ul>

            @endauth
        @endif
    </div>
    </nav>
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
                        } else if (data['status'] == "not_logged_in"){
                            //if the user is not logged in, tells it to not do anything
                        }
                        else {
                            Swal.fire("Error", "There was an error, please try again!", "error");
                        }
                    },
                    error: function ( xhr, errorType, exception ) {
                        var errorMessage = exception || xhr.statusText;
                        Swal.fire("Error", "There was a connectivity problem. Please try again.", "error");
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
