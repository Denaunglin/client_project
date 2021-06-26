@extends('backend.admin.layouts.app')
@section('meta_title', 'Room Detail')
@section('page_title', 'Room Detail')
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
                        <div class="banner-imgs col-md-6 offset-md-3 col-sm-12">
                            <img src="{{$room_detail->image_path()}}" alt="Image 1">
                        </div>

                        <div class="dates">
                            <div class="start">
                                <strong>Created Date</strong> {{$room_detail->created_at}}
                                <span></span>
                            </div>
                            <div class="ends">
                                <strong>Updated Date</strong> {{$room_detail->updated_at}}
                            </div>
                        </div>

                        <div class="stats">

                            <div>
                                <strong>Room Type </strong> {{$room_detail->roomtype->name}}
                            </div>

                            <div>
                                <strong>Bed Type</strong> {{$room_detail->bedtype->name}}
                            </div>

                            <div>
                                <strong>Room Qty</strong> {{$room_detail->room_qty}}
                            </div>
                        </div>

                        <div class="stats">
                            <div>
                                <strong>Adult Qty</strong> {{$room_detail->adult_qty}}
                            </div>

                            <div>
                                <p><strong>Myanmar Price</strong> {{$room_detail->price}} MMK /Night</p>
                            </div>

                            <div>
                                <p><strong>Foreign Price</strong> $ {{$room_detail->foreign_price}} /Night</p>
                            </div>

                        </div>
                        <div class="stats">
                            <div>
                                <strong>Extra Bed Qty</strong> {{$room_detail->extra_bed_qty}}
                            </div>

                            <div>
                                <p><strong>Extra Bed MM Price</strong> {{$room_detail->extra_bed_mm_price}} MMK /Night</p>
                            </div>

                            <div>
                                <p><strong>Extra Bed Foreign Price</strong> $ {{$room_detail->extra_bed_foreign_price}} /Night</p>
                            </div>

                        </div>
                        <div class="stats">
                            <div>
                                <strong>Early Check In</strong>
                            </div>

                            <div>
                                <p><strong>Early Check-In MM Price</strong> {{$room_detail->early_checkin_mm}} MMK /Night</p>
                            </div>

                            <div>
                                <p><strong>Early Check-In Foreign Price</strong> $ {{$room_detail->early_checkin_foreign}} /Night</p>
                            </div>

                        </div>
                        <div class="stats">
                            <div>
                                <strong>Late Check Out</strong>
                            </div>

                            <div>
                                <p><strong>Late Check-Out MM Price</strong> {{$room_detail->late_checkout_mm}} MMK /Night</p>
                            </div>

                            <div>
                                <p><strong>Late Check-Out Foreign Price</strong> $ {{$room_detail->late_checkout_foreign}} /Night</p>
                            </div>

                        </div>

                        <div class="text-center p-5">
                            <strong>Description</strong>

                            <p class="mt-3" style="white-space:pre-wrap;">{{$room_detail->description}}</p>
                        </div>
                        <hr>

                        <div class="text-center py-3">
                            <strong>Facilities</strong></br>

                            @foreach($facilities as $data)

                            <span class="badge badge-info mt-3">{{$app[$data]}}</span>

                            @endforeach

                        </div>
                        <hr>

                        <div class="text-center py-3 my-3">
                            <p><strong>Room Layout</strong></p>
                            @if(count($room_detail->roomlayout))
                            <div class="row">
                                @forelse($room_detail->roomlayout as $roomlayout)
                                <div class="col-md-3 mb-2">
                                    <div class="card border-info">
                                        <div class="card-body">
                                            <p class="mb-0">Room Number - {{$roomlayout->room_no}}</p>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                @endforelse
                            </div>
                            @endif
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
