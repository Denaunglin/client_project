@extends('backend.admin.layouts.app')
@section('meta_title', 'Credit Report Detail')
@section('page_title', 'Credit Report Detail')
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
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="wrapper">
                        <div class="dates">
                            <div class="start">
                                <strong class="text-dark">Created Date</strong> {{$credit->created_at}}
                                <span></span>
                            </div>
                            <div class="ends">
                                <strong class="text-dark">Updated Date</strong> {{$credit->updated_at}}
                            </div>
                        </div>
                        
                        <div class="stats">
                            <div>
                                <strong class="text-dark">Customer </strong> <br>
                                <ul class="list-group text-left">
                                    <li class="list-group-item">Name - {{$credit->customer ? $credit->customer->name : '-'}}</li>
                                    <li class="list-group-item">Phone - {{$credit->customer ? $credit->customer->phone : '-'}}</li>
                                    <li class="list-group-item">Address - {{$credit->customer ? $credit->customer->address : '-'}}</li>
                                </ul>
                            </div> 
                                <div>
                                    <strong class="text-dark">Item Information </strong> <br>
                                    <ul class="list-group">
                                        @foreach($item_data as $data)
                                        <li class="list-group-item">{{$data->name}} / ({{$data->retail_price}} MMK)</li>
                                        @endforeach
                                    </ul>
                                </div>
                            <div>
                                <strong class="text-dark">Credit Information </strong> <br>
                                <ul class="list-group text-left">
                                    
                                    <li class="list-group-item">Origin Amount - {{$credit->origin_amount }}</li>
                                    <li class="list-group-item">Paid Amount - {{$credit->paid_amount }}</li>
                                    <li class="list-group-item">Credit Amount - {{$credit->credit_amount}}</li>
                                    <li class="list-group-item">Paid Date - {{$credit->paid_date}}</li>
                                    <li class="list-group-item">Paid Times - {{$credit->paid_times}}</li>
                                    @if($credit->paid_status == 0)
                                    <li class="list-group-item">Paid Status - <span class="badge badge-success">Paided</span></li>
                                    @else
                                    <li class="list-group-item">Paid Status - <span class="badge badge-warning">UnPaid</span></li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
@endsection
