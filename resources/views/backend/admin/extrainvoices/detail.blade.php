@extends('backend.admin.layouts.app')
@section('meta_title', 'Invoice Detail')
@section('page_title', 'Invoice Detail')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('style')
<link rel="stylesheet" href="{{asset('assets/css/backend_room_detail.css')}}">
@endsection
@section('content')
@include('layouts.errors_alert')
<div class="card mb-3">
    <div class="card-body">
<iframe src="{{$extrainvoice->pdf_path()}}" width="100%" height="600"></iframe>    </div>
</div>
@endsection
@section('script')
{!! JsValidator::formRequest('App\Http\Requests\RoomFormRequest', '#form') !!}
@endsection
