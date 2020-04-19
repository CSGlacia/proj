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
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    
    <!-- Sweet Alert -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.css" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.js"></script>

    <!-- Datepicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

    <!-- Moment.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>

    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/js/select2.full.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.12/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css">

    <!-- lightslider -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightslider/1.1.6/js/lightslider.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/lightslider/1.1.6/css/lightslider.css">
    <link rel="stylesheet" type="image" href="https://cdnjs.cloudflare.com/ajax/libs/lightslider/1.1.6/img/controls.png">

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
        width:100%;
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


    /* Style the input container */
    .input-container {
      display: flex;
      width: 100%;
      margin-bottom: 15px;
    }

    /* Style the input fields */
    .input-field {
      width: 100%;
      padding: 10px;
      outline: none;
    }

    .input-icon {
      padding: 10px;
      min-width: 50px;
      text-align: center;
      background-color: #c6c6c6;
      border-top-left-radius:5px;
      border-bottom-left-radius:5px;
      margin-right:-5px;
    }


    #img_modal {
      border-radius: 5px;
      cursor: pointer;
      transition: 0.3s;
    }


    /* The Modal (background) */
    .modal {
      display: none; /* Hidden by default */
      position: fixed; /* Stay in place */
      z-index: 1; /* Sit on top */
      padding-top: 100px; /* Location of the box */
      left: 0;
      top: 0;
      width: 100%; /* Full width */
      height: 100%; /* Full height */
      overflow: auto; /* Enable scroll if needed */
      background-color: rgb(0,0,0); /* Fallback color */
      background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
    }

    /* Modal Content (Image) */
    .modal-content {
      margin: auto;
      display: block;
      width: 80%;
      max-width: 700px;
    }

    /* Add Animation - Zoom in the Modal */
    .modal-content {
      animation-name: zoom;
      animation-duration: 0.6s;
    }

    @keyframes zoom {
      from {transform:scale(0)}
      to {transform:scale(1)}
    }

    /* The Close Button */
    .close {
      position: absolute;
      top: 15px;
      right: 35px;
      color: #f1f1f1;
      font-size: 40px;
      font-weight: bold;
      transition: 0.3s;
    }

    .close:hover,
    .close:focus {
      color: #bbb;
      text-decoration: none;
      cursor: pointer;
    }

    /* 100% Image Width on Smaller Screens */
    @media only screen and (max-width: 700px){
      .modal-content {
        width: 100%;
      }
    }

</style>
@yield('style')


<body class="page-holder bg-cover nav-header">
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
    <div class="container">

        <a class="navbar-brand" href="/">TURTLE</a>
            @if (Route::has('login'))
            @auth
            
                <ul class="navbar-nav">
                    @if(auth()->user()->can('delete bookings') || auth()->user()->can('delete reviews'))
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                          Admin
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            @can('delete bookings')
                            <a class="dropdown-item" href="/list_bookings">View All Bookings</a>
                            @endcan
                            @can('delete reviews')
                            <a class="dropdown-item " href="/list_reviews">View All Reviews</a>
                            @endcan
                        </div>
                    </li>
                    @endif
                    <li class="nav-item"><a href="/create_property_page" class="nav-link">Add Property</a></li>
                    <li class="nav-item"><a href="/create_property_listing" class="nav-link">Add Listing(s)</a></li>
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
