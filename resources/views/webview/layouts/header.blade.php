<div class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <span class="email"><a href="mailto:info@starfishmyanmar.com" target="_blank"><i
                            class="fas fa-envelope"></i>
                        Partnership Inquiry : info@starfishmyanmar.com </a></span> &emsp;
                {{-- <span class="phone"><a href="tel:09256328604 "><i class="fas fa-phone"></i> 09-256328604</span> --}}
            </div>

            <div class="col-lg-4 text-lg-right text-left">
                @guest
                <span>
                    <a href="{{('/login')}}"><i class="fas fa-user-circle"></i>&nbsp; Login</a>
                </span>
                @else
                <div class="profile-dropdown">
                    <span style="color:white;" class="fa fa-user-circle"> {{Auth::user()->name}}
                    </span>
                    <div class="profile-dropdown-content text-left">
                        <div class="nav__menu__arrow" style="left: 105px; right: auto;"></div>
                        <div class="mb-3">
                            <a href="{{('/clientprofile')}}"> My Account</a>
                        </div>
                        <div class="mb-3">
                            <a href="{{ url('/profile/update') }}">Edit Profile</a>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <a href="{{ url('/client/notification') }}">Notifications</a>
                        </div>
                        <hr>
                        <span>
                            <a href="{{ route('logout') }}" onclick="logout()">
                                <span> Logout</span>
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                                <input type="hidden" value="" id="signal_id" name="signal_id">
                            </form>

                        </span>
                    </div>
                </div>
                @endguest
                &emsp;
                <span class="text-white">|</span>
                &emsp;
                <span>
                    <a href="https://www.facebook.com/ApexHotelNayPyiTaw/" target="_blank"><i
                            class="fab fa-facebook"></i></a>
                </span>
            </div>

        </div>
    </div>
</div>

<nav class="fixed-header navbar navbar-expand-xl navbar-trans navbar-inverse main-nav">
    <div class="container">
        <a class="navbar-brand" href="{{url('/')}}">
            <img src="{{asset('images/hotel_logo.png')}}">
        </a>

        <!-- Toggler/collapsibe Button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbar">
            <span class="navbar-toggler-icon fa fa-bars"></span>
        </button>

        <!-- Navbar links -->
        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link " href="{{url('/')}}">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link"
                        href="{{url('room/list')}}?check_in_date={{Carbon\Carbon::now()->format('Y-m-d')}}&check_out_date={{Carbon\Carbon::now()->addDay()->format('Y-m-d')}}&room_qty=1&guest=1&nationality=1&extra_bed_qty=0">Room</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link " href="{{url('/booking/retrieve')}}">Retrieve Booking</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link " href="{{url('/policies')}}"> Policies</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link " href="{{url('/about-us')}}">About Us</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link " href="{{url('/contact-us')}}">Contact Us</a>
                </li>

                <li class="nav-item">
                    <a href="{{url('room/list')}}?check_in_date={{Carbon\Carbon::now()->format('Y-m-d')}}&check_out_date={{Carbon\Carbon::now()->addDay()->format('Y-m-d')}}&room_qty=1&guest=1&nationality=1&extra_bed_qty=0"
                        class="btn btn-book">Book Now</a>
                </li>
                <li class="nav-item">
                    <div class="dropdown">
                        <button class="btn btn-book dropdown-toggle" type="button" data-toggle="dropdown">Other Services
                            <span class="caret"></span></button>
                        <ul class="dropdown-menu text-left">
                            <a href="{{env('APEX_GAS_OIL_URL')}}" class="dropdown-item" type="button">Fuel
                                Wholesales</a>
                            <a href="{{env('ANAWARHLWAM_URL')}}" class="dropdown-item" type="button">Fish & Prawn
                                Market</a>
                            <a href="{{env('STAR_FISH_MYANMAR_URL')}}" class="dropdown-item" type="button">First AI
                                Myanmar</a>
                            <a href="#" class="dropdown-item" type="button"></a>
                            <li class="dropdown-submenu pl-4">
                                <a class="test  dropdown-toggle " tabindex="-1" href="#"><span class="nav-label">Travel
                                        & Tours Services</span><span class="fa fa-arrow"></span></a>
                                <ul class="dropdown-menu sub text-left">
                                    <li><a href="{{env('APEX_HOTEL_URL')}}">Apex Hotel</a></li>
                                    <li><a href="{{env('DAWEI_RESORT_URL')}}">Dawei Resort</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </li>
                @if(Auth::user())
                <li class="nav-item .d-none .d-sm-block d-md-block d-lg-none d-xl-none ">
                    <div class="dropdown">
                        <button class="btn btn-book dropdown-toggle" type="button" data-toggle="dropdown">
                            {{Auth::user()->name}}
                            <span class="caret"></span></button>
                        <ul class="dropdown-menu text-left">
                            <a href="{{('/clientprofile')}}" class="dropdown-item" type="button"> My Account </a>
                            <hr>
                            <a href="{{ url('/profile/update') }}">Edit Profile</a>
                            <hr>
                            <a href="{{ url('/client/notification') }}">Notifications</a>
                            <hr>
                            <span>
                                <a href="{{ route('logout') }}" id="unsubscribe" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                    <span> Logout</span>
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    <input type="hidden" value="" id="signal_id" name="signal_id">
                                    @csrf
                                </form>
                            </span>
                        </ul>
                    </div>
                </li>

                @else

                <li class="nav-item .d-none .d-sm-block d-md-block d-lg-none d-xl-none">
                    <a href="{{url('/login')}}" class="btn btn-book login-btn">Login</a>
                </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

<nav id="navbar" class=" fixed-header navbar navbar-expand-xl navbar-trans navbar-inverse secondary-nav">

    <div class="second_nav container">
        <a class="navbar-brand" href="{{url('/')}}">
            <img src="{{asset('images/hotel_logo.png')}}" width="90px" height="70px">
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#secondNavbar">
            <span class="navbar-toggler-icon fa fa-bars"></span>
        </button>

        <!-- Navbar links -->
        <div class="collapse second_nav navbar-collapse" id="secondNavbar">

            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link " href="{{url('/')}}">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link "
                        href="{{url('room/list')}}?check_in_date={{Carbon\Carbon::now()->format('Y-m-d')}}&check_out_date={{Carbon\Carbon::now()->addDay()->format('Y-m-d')}}&room_qty=1&guest=1&nationality=1&extra_bed_qty=0">Room</a>
                </li>

                <li class="nav-item active">
                    <a class="nav-link " href="{{url('/booking/retrieve')}}">Retrieve Booking</a>
                </li>

                <li class="nav-item active">
                    <a class="nav-link " href="{{url('/policies')}}"> Policies</a>
                </li>

                <li class="nav-item active">
                    <a class="nav-link " href="{{url('/about-us')}}">About Us</a>
                </li>

                <li class="nav-item active">
                    <a class="nav-link " href="{{url('/contact-us')}}">Contact Us</a>
                </li>

                <li class="nav-item ml-3">
                    <a href="{{url('room/list')}}?check_in_date={{Carbon\Carbon::now()->format('Y-m-d')}}&check_out_date={{Carbon\Carbon::now()->addDay()->format('Y-m-d')}}&room_qty=1&guest=1&nationality=1&extra_bed_qty=0"
                        class="btn btn-book">Book Now</a>
                </li>

                <li class="nav-item">
                    <div class="dropdown">
                        <button class="btn btn-book dropdown-toggle" type="button" data-toggle="dropdown">Other Services
                            <span class="caret"></span></button>
                        <ul class="dropdown-menu text-left">
                            <a href="{{env('APEX_GAS_OIL_URL')}}" class="dropdown-item" type="button">Fuel
                                Wholesales</a>
                            <a href="{{env('ANAWARHLWAM_URL')}}" class="dropdown-item" type="button">Fish & Prawn
                                Market</a>
                            <a href="{{env('STAR_FISH_MYANMAR_URL')}}" class="dropdown-item" type="button">First AI
                                Myanmar</a>
                            <a href="#" class="dropdown-item" type="button"></a>
                            <li class="dropdown-submenu pl-4">
                                <a class="test  dropdown-toggle " tabindex="-1" href="#"><span class="nav-label">Travel
                                        & Tours Services</span><span class="fa fa-arrow"></span></a>
                                <ul class="dropdown-menu sub text-left">
                                    <li><a href="{{env('APEX_HOTEL_URL')}}">Apex Hotel</a></li>
                                    <li><a href="{{env('DAWEI_RESORT_URL')}}">Dawei Resort</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
