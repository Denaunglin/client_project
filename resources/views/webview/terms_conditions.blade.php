@extends('webview.layouts.app')
@section('title', 'Hotel Terms and Conditions')
@section('content')
<section class="breadcrumb-outer">
    <div class="container">
        <div class="breadcrumb-content">
            <h2>@lang("message.terms_conditions")</h2>
            <nav aria-label="breadcrumb">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">@lang('message.header.home')</a></li>
                    <li class="breadcrumb-item active" aria-current="page">@lang('message.terms_conditions')</li>
                </ul>
            </nav>
        </div>
    </div>
</section>
<section class="policies">
    <div class="container">
        <div class="section-title">
            <h2>@lang('message.hotel_term_conditon_title')</h2>
        </div>
        <div class="row">
            <div class="col-12 col-sm-12 table-responsive">
                <div class="col-12 col-sm-12 table-responsive">
                    <table class="table table-striped table-bordered">
                        <tbody>
                            <tr>
                                <h5> <b>Apex Hotel Myanmar</b> </h5>
                                <h6 >
                                    @lang("message.terms_conditions_subtitle")
                                </h6>
                                <p >
                                @lang("message.terms_conditons_text1")
                                </p>
                                <hr>
                                    <h6><b>* @lang("message.become_member") <b></h6>
                                <p>
                                    @lang("message.become_memeber_text")
                                </p>
                            </tr>
                            <tr>
                                <td><i class="fa fa-calendar"></i>&nbsp; @lang('message.checkin_time')
                                </td>
                                <td>- @lang('message.from')</td>
                            </tr>
                            <tr>
                                <td><i class="fa fa-calendar"></i>&nbsp; @lang('message.checkout_time')
                                </td>
                                <td>- @lang('message.until')</td>
                            </tr>
                            <hr>
                            <tr>
                                <h6><b>* @lang('message.booking_reservation') </b> </h6>
                                <p> @lang('message.booking_reservation_text')</p> 
                            <hr>
                                <h6><b>* @lang('message.cancellation_policy') </b></h6>   
                                <p>- @lang('message.cancellation_policy_text')</p>
                            <hr>
                            <h6> <b>* @lang('message.deposite_policy') </b></h6>
                            <p>- @lang('message.deposite_policy_text')</p>
                            <hr>
                            <h6><b>* @lang('message.pet_policy') </b></h6> 
                            <p> @lang('message.pets_text')</p>
                            </tr>
                        </tbody>
                </table>                
              </div>
            </div>
        </div>
    </div>
</section>
@endsection
