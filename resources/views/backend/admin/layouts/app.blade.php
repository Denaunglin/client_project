<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="{{ str_replace('_', '-', app()->getLocale()) }}">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="robots" content="noindex, nofollow">
    <meta name="msapplication-tap-highlight" content="no">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
    <meta name="description" content="@yield('meta_desc')">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('meta_title') | {{ config('app.name') }}</title>
    <link rel="icon" href="{{asset('images/phonshop.jpg')}}">


    {{-- Language --}}
    @if(App()->isLocale('en'))
    <link href="{{asset('assets/css/lang_en.css')}}" rel="stylesheet">
    @elseif(App()->isLocale('mm_zg'))
    <link href="{{asset('assets/css/lang_mm_zg.css')}}" rel="stylesheet">
    @elseif(App()->isLocale('mm_uni'))
    <link href="{{asset('assets/css/lang_mm_uni.css')}}" rel="stylesheet">
    @endif

    
    @include('backend.admin.layouts.assets.css')

    @yield('extra_css')
    <style>
        .ps__thumb-y{
            background-color:rgb(136, 129, 129) !important;
            width:15px !important;
        }
    </style>

    {{-- Recaptcha --}}
    <script src='https://www.google.com/recaptcha/api.js'></script>

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
    <div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header ">
        @include('backend.admin.layouts.header')

        <div class="app-main">
            @include('backend.admin.layouts.sidebar')
            <div class="app-main__outer">
                <div class="app-main__inner">
                    @include('backend.admin.layouts.components.page_title')

                    <div class="py-3">
                        <div class="d-inline-block">
                            <button class="previous-btn btn btn-dark"> <i class="fas fa-arrow-circle-left"></i>
                                @lang("message.back")</button>
                        </div>
                    </div>

                    @yield('content')
                </div>
                @include('backend.admin.layouts.footer')
            </div>
        </div>
    </div>
  

    @include('menu_search.menu_search_script')
    @include('backend.admin.layouts.assets.js')
    <script>
      
        // $('#searchModal').on('click', function(e) {
        //     $('#input').val('');
        //     $("#menu-search-result-list").html('');
        //     $("#menu-search-result-container").attr('hidden', true);
        // });

        $('#searchModal').on('show', function () {
        $('input:text:visible:first').focus();
        });

         $('#input').change(function(e) {
            let search = $(this).val();
          console.log(search);
            $.get('/get_item?search=' + search, function(data) {
        console.log(data);
        let result = '';
           
            if(data.length) {
                $("#menu-search-result-container").attr('hidden', false);
                $.each(function(e) {
                    if($(this).text().toLowerCase().includes(text.toLowerCase())) {
                       result += '<li class="list-group-item">' + $(this).prop("outerHTML") + '</li>';
                    }
                });
              
                $("#menu-search-result-list").html(result);
            } else {
                $("#menu-search-result-msg").text('No Item Found.');
                $("#menu-search-result-container").attr('hidden', false);
                $("#menu-search-result-list").html('');
                // $("#menu-search-result-msg").html('');
            }
                    $.each(data, function( key, value ) {
                        if(value.qty == 0){
                            $calss = "badge-danger";
                        }else{
                            $calss = "badge-info";
                        }
                     $('#menu-search-result-list').append('<li class="list-item" style="list-style-type:none;" ><div class="card m-1 shadow"><h5 class="m-3"> <span class="text-muted">'+value.name+' <span> <br> <span class="text-primary">'+value.retail_price+' MMK (Retail) </span> <i class="badge '+$calss+'">'+value.qty+'</i>  </h5></div></li>');
            });
        });
    });
    </script>
</body>

</html>
