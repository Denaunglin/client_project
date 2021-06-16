@extends('webview.layouts.app')
@section('title', 'Hotel Policies')
@section('content')
<section class="breadcrumb-outer">
    <div class="container">
        <div class="breadcrumb-content">
            <h2>@lang('message.header.policies')</h2>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">@lang('message.header.home')</a></li>
                    <li class="breadcrumb-item active" aria-current="page">@lang('message.header.policies')</li>
                </ul>
            </nav>
        </div>
    </div>
</section>
<section class="policies">
    <div class="container">
        <div class="section-title">
            <h2><strong>@lang('message.hotel')</strong> <span><strong>@lang('message.policy')</strong></span></h2>
        </div>
       <div class="row">
            <div class="col-12 col-sm-12 table-responsive">
                <table class="table table-striped table-bordered">
                    <tbody>
                        <tr>
                            <td><i class="fa fa-calendar blue_fa"></i>&nbsp; @lang('message.check-in')</td>
                            <td> @lang('message.from') </td>
                        </tr>   
                        <tr>
                            <td><i class="fa fa-calendar blue_fa"></i>&nbsp; @lang('message.check-out')</td>
                            <td> @lang('message.until')  </td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-info-circle blue_fa"> </i>&nbsp;@lang('message.cancellation')/@lang('message.prepayment')</td>
                            <td>@lang('message.cancellation_prepayment_text')</td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-bed blue_fa"></i>&nbsp; @lang('message.children_beds')</td>
                            <td> @lang('message.cancellation_text') </td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-dog blue_fa"></i>&nbsp; @lang('message.pets')</td>
                            <td> @lang('message.pets_text')</td>
                        </tr>
                        <tr>
                            <td><i class="fa fa-credit-card blue_fa"></i>&nbsp;@lang('message.cash_only')</td>
                            <td>@lang('message.cash_only_text')</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
