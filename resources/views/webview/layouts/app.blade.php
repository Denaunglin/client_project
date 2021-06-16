<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{asset('images/hotel_logo.png')}}">
    <title>@yield('title') - Apex Hotel</title>
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Lato|Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('fonts/vendor/fontawesome/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bst/css/bootstrap.css') }}">

    <!-- Styles -->
    @if(request()->theme == "dark")
    <link rel="stylesheet" href="{{asset('assets/css/webview.css')}}">
    @else
    <link rel="stylesheet" href="{{asset('assets/css/main.css')}}">
    @endif
    <link rel="stylesheet" href="{{asset('assets/css/mediaquery.css')}}">


    {{-- Language --}}
    @if(App()->isLocale('en'))
    <link href="{{asset('assets/css/lang_en.css')}}" rel="stylesheet">
    @elseif(App()->isLocale('mm_zg'))
    <link href="{{asset('assets/css/lang_mm_zg.css')}}" rel="stylesheet">
    @elseif(App()->isLocale('mm_uni'))
    <link href="{{asset('assets/css/lang_mm_uni.css')}}" rel="stylesheet">
    @endif

    @yield('extra_css')

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-158063920-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-158063920-3');
    </script>
</head>

<body>
    <div>
        @yield('content')
    </div>


    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js">
    </script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

    {{-- Daterange picker --}}
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    {{-- Js Validation --}}
    <script src="{{asset('vendor/jsvalidation/js/jsvalidation.min.js')}}"></script>

    {{-- Sweet alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

    @yield('script')
</body>

</html>
