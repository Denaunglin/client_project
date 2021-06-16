@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit Notification')
@section('page_title', 'Edit Notification ')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
     <div class="col-md-12 ">
                    <div class="card">
                        <div class="card-body text-center m-5">
                            <h5 style="color:#44da46;"  style=" font-family: 'Anton', sans-serif; font-family: 'Hammersmith One', sans-serif;" class="text-left">{{$sendnotification->title}} </h5>
                            <p class="text-right" >(Date : {{$sendnotification->created_at->format('d-m-Y')}})</p>
                            <hr>
                            <p class="mb-5" style="white-space:pre-wrap;">{{$sendnotification->description}}</p>
                        </div>
                        
                    </div>
                </div>
</div>

@endsection

@section('script')
{!! JsValidator::formRequest('App\Http\Requests\StoreBedType', '#form') !!}
@endsection
