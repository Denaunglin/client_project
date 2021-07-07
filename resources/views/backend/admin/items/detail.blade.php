@extends('backend.admin.layouts.app')
@section('meta_title', 'Item Detail')
@section('page_title')
@lang("message.header.item")
@endsection
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('style')
<link rel="stylesheet" href="{{asset('assets/css/backend_items.css')}}">
@endsection
@section('content')
@include('layouts.errors_alert')
<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="tile">
                    <div class="wrapper">
                        <div class="banner-imgs col-md-6 offset-md-3 col-sm-12">
                        </div>

                        <div class="dates">
                            <div class="start">
                                <strong>@lang("message.header.created_at")</strong>  : {{$items->created_at}}
                                <span></span>   
                            </div>
                            <hr>
                            <div class="ends">
                                <strong>@lang("message.header.updated_at")</strong> :  {{$items->updated_at}}
                            </div>
                            <hr>
                        </div>

                        <div class="stats">

                            <div>
                                <strong> @lang("message.name") </strong>  : {{$items->name}}
                            </div>
<hr>
                          <hr>

                         
                            <div>
                                <strong>@lang("message.header.item_category")</strong>  : {{$items->item_category->name}}
                            </div>
                            <hr>

                            <div>
                                <strong>@lang("message.header.item_sub_category")</strong> :  {{$items->item_sub_category->name}}
                            </div>
                            <hr>

                            <div>
                                <strong>@lang("message.header.minimun_qty")</strong> :  {{$items->minimun_qty}}
                            </div>
                            <hr>

                            <div>
                                <strong>@lang("message.header.buying_price")</strong> :  {{$items->buying_price}}
                            </div>
                            <hr>

                            <div>
                                <strong>@lang("message.header.retail_price")</strong> : {{$items->retail_price}}
                            </div>
                            <hr>

                            <div>
                                <strong>@lang("message.header.wholesale_price")</strong> : {{$items->wholesale_price}}
                            </div>
                            <hr>

                        </div>

                        <hr>

                      

                        <div class="footer">
                            <a href="{{url('admin/rooms')}}" class="Cbtn Cbtn-primary">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
{!! JsValidator::formRequest('App\Http\Requests\RoomFormRequest', '#form') !!}
@endsection
