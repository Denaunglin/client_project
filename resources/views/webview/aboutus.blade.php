@extends('webview.layouts.app')
@section('title', 'About Us')
@section('content')
<section class="breadcrumb-outer">
    <div class="container">
        <div class="breadcrumb-content">
            <h2> @lang('message.header.about_us')</h2>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">@lang('message.header.home')</a></li>
                    <li class="breadcrumb-item active" aria-current="page"> @lang('message.header.about_us')</li>
                </ul>
            </nav>
        </div>
    </div>
</section>
<section class="about">
    <div class="container">
        <div class="row mb-5">
            <div class="col-md-8 mb-3">
                <div class="about-para">
                    <h2><strong>@lang("message.discover")</strong>
                        <span><strong>@lang('message.discover_apex_hotel')</strong></span></h2>
                    <p>
                        &emsp; @lang("message.abouthotel.about_hotet_pone")
                    </p>
                    <p>
                        &emsp; @lang("message.abouthotel.about_hotet_ptwo")

                    </p>
                    <p>
                        &emsp; @lang("message.abouthotel.about_hotel_pthree") </p>
                    </p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="p-4">
                    <img src="{{asset('images/apex_hotel.jpg')}}" alt="apex hotel image" width="100%">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 mb-3">
                <div class="card border-0">
                    <div class="card-body p-0">
                        <div class="nearby mb-5">
                            <h5 class="mb-3"><i class="fa fa-map-marker-alt"></i> &nbsp; @lang('message.what_nearby')
                            </h5>
                            <p>@lang("message.shwe_see_gon") - 15 @lang("message.min_walk")</p>
                            <p>@lang("message.yan_aung_myin") - 3 @lang("message.min_drive")</p>
                            <p>@lang("message.yanaungmyin_golf") - 5 @lang("message.min_drive")</p>
                            <p>@lang("message.zoo_safari")- 11 @lang("message.min_drive")</p>
                            <p>@lang("message.gen_museum") - 11 @lang("message.min_drive")</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-4 col-xs-4">
                <div class="card border-0">
                    <div class="card-body p-0">
                        <div class="nearby mb-5">
                            <h5 class="mb-3"><i class="fa fa-car"></i> &nbsp; @lang('message.abouthotel.getting_around')
                            </h5>
                            <p>Naypyidaw (NYT-Naypyidaw Intl.) - 18 min drive</p>
                        </div>
                        <div class="nearby mb-5">
                            <h5 class="mb-3"><i class="fa fa-utensils"></i> &nbsp;
                                @lang("message.abouthotel.restaurants")</h5>
                            <p> Onsite dining venue – Restaurant offering international cuisine. Serves breakfast and
                                lunch daily.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('script')
@endsection
