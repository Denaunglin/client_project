@extends('backend.admin.layouts.app')
@section('meta_title', 'Add Booking')
@section('page_title', 'Add Booking')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('extra_css')
<style>
    .pay-img {
        width: 14% !important;
        height: auto;
    }

    .master-img {
        width: 5% !important;
        height: auto;
    }
</style>
@endsection
@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.booking.store') }}" method="post" id="create">
                    @csrf
                    <input type="hidden" name="room_qty" value="{{request()->room_qty}}">
                    <input type="hidden" name="guest" value="{{request()->guest}}">
                    <input type="hidden" name="check_in" value="{{request()->check_in_date}}">
                    <input type="hidden" name="check_out" value="{{request()->check_out_date}}">

                    <h5>Booking Information</h5>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Room</label>
                                <select name="room_id" id="roomtype_id" class="form-control select2">
                                    <option value="">-- Please Choose --</option>
                                    @foreach($rooms as $room)
                                        @php
                                        $checkin= Carbon\Carbon::now()->format('Y-m-d');
                                        $checkout= Carbon\Carbon::now()->addDay()->format('Y-m-d');
                                        $avaliable_room_qty =  App\Helper\ResponseHelper::avaliable_room_qty($room, $checkin, $checkout);
                                        @endphp
                                    <option  data-id1="{{$room->extra_bed_qty}}" data-id3="{{$avaliable_room_qty}}" data-id4="{{$room->adult_qty}}" value="{{$room->id}}">Room Type -
                                        {{$room->roomtype ? $room->roomtype->name : '-'}} , Bed Type -
                                        {{$room->bedtype ? $room->bedtype->name : '-'}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Checkin - Checkout</label>
                                <input name="checkin_checkout" class="form-control checkin_checkout">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Guest</label>
                                <select name="guest" class="form-control select2">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                     <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>

                                </select>
                                @error('guest')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Room Qty</label>
                                <select name="room_qty" id="room_qty" class="form-control select2">
                                      @php 
                                    $room_qty = 0;
                                    @endphp
                                   @for($i = 0; $i <= $room_qty ; $i++) <option value="{{$i}}"
                                        >{{$i}}
                                        </option>
                                    @endfor
                                </select>
                                @error('room_qty')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Extra Bed Qty</label>
                                <select name="extra_bed_qty" id="extra_bed_qty" class="form-control select2">
                                  @php 
                                    $extra_bed_qty = 0;
                                    @endphp
                                   @for($i = 0; $i <= $extra_bed_qty ; $i++) <option value="{{$i}}"
                                        >{{$i}}
                                        </option>
                                    @endfor
                                </select>
                                @error('extra_bed_qty')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <hr>

                    <h5>Early Checkin & Late Checkout</h5>
                    <div class="row mb-3">
                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input value="1" class=" form-check-input" type="checkbox" name="early_late[]"
                                    id="defaultCheck1">
                                <label class="form-check-label" for="defaultCheck1">
                                    Early Check-In
                                </label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input value="2" class=" form-check-input" type="checkbox" name="early_late[]"
                                    id="defaultCheck1">
                                <label class="form-check-label" for="defaultCheck1">
                                    Late Check-Out
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label> Early Check-In Time</label>
                            <input type="time" class="form-control timepicker" name="early_checkin_time">

                        </div>

                        <div class="col-md-6">
                            <label>Late Check-Out Time</label>
                            <input type="time" class="form-control timepicker" name="late_checkout_time">
                        </div>
                    </div>
                    <hr>

                    <h5>Payment Information</h5>
                    <div class="row">
                        {{-- <div class="col-md-12 mb-3">
                            <input value="1" name="pay_method" type="checkbox" data-toggle="collapse"
                                href="#collapseExample" role="button" aria-expanded="false"
                                aria-controls="collapseExample" class="pay-list form_control"> Pay with Credit Card

                            <img src="{{asset('images/mastercard.png')}}" class="master-img">
                        <div class="collapse" id="collapseExample">
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <label>SELECT CREDIT CARD TYPE *</label>
                                    <select class="form-control" name="credit_type">
                                        @forelse($cardtype as $data)
                                        <option value="{{$data->id}}" @if(Auth::user())@if(Auth::user()->
                                            usercreditcard)
                                            @if(Auth::user()->usercreditcard->credit_type==$data->id) selected
                                            @endif @endif @endif
                                            >{{$data->name}}</option>
                                        @empty
                                        <p>There is no data</p>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <label>CREDIT CARD NUMBER *</label>
                                    <input type="number" class="form-control"
                                        @if(Auth::user())@if(Auth::user()->usercreditcard)
                                    value="{{Auth::user()->usercreditcard->credit_no}}" @endif @endif
                                    name="credit_no">
                                </div>
                                <div class="col-md-6 mt-4">
                                    <label>CREDIT CARD EXPIRATION DATE *</label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Month</label>
                                            <select class="form-control" name="expire_month">

                                                @foreach($month as $key => $data)
                                                <option value="{{$key}}" @if(Auth::user()) @if(Auth::user()->
                                                    usercreditcard)
                                                    @if(Auth::user()->usercreditcard->expire_month==$key) selected
                                                    @endif @endif
                                                    @endif>{{$data}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label>Year</label>
                                            <select class="form-control" name="expire_year">
                                                @foreach($year as $key => $data)
                                                <option value="{{$key}}" @if(Auth::user()) @if(Auth::user()->
                                                    usercreditcard)
                                                    @if(Auth::user()->usercreditcard->expire_year==$key) selected
                                                    @endif @endif
                                                    @endif>{{$data}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                    <div class="col-md-12 mb-3 mt-3">
                        <div class="row">
                            {{-- <div class="col-md-4 mb-3 ">
                                    <div class="form-check">
                                        <input value="2" class="pay-list form-check-input" type="checkbox"
                                            name="pay_method" id="defaultCheck1">
                                        <label class="form-check-label" for="defaultCheck1">
                                            KBZ-Pay
                                        </label> &emsp13;
                                        <img src="{{asset('images/kbzpay.png')}}" class="pay-img">
                        </div>
                    </div> --}}
                    {{-- <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input value="3" class="pay-list form-check-input" type="checkbox"
                                            name="pay_method" id="defaultCheck1">
                                        <label class="form-check-label" for="defaultCheck1">
                                            AyA-Pay
                                        </label>
                                        &emsp13;
                                        <img src="{{asset('images/ayapay.jpeg')}}" class="pay-img">
                            </div>
                        </div> --}}
                       {{-- <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input value="4" class="pay-list form-check-input" type="checkbox"
                                            name="pay_method" id="defaultCheck1">
                                        <label class="form-check-label" for="defaultCheck1">
                                            CB-Pay
                                        </label> &emsp13;
                                        <img src="{{asset('images/cbpay.png')}}" class="pay-img">
                            </div>
                        </div> --}}
                        {{-- <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input value="5" class="pay-list form-check-input" type="checkbox"
                                            name="pay_method" id="defaultCheck1">
                                        <label class="form-check-label" for="defaultCheck1">
                                            Wave-Pay
                                        </label> &emsp13;
                                        <img src="{{asset('images/wavemoney.png')}}" class="pay-img">
                            </div>
                            </div> --}}
                            <div class="col-md-4 mb-3">
                                <div class="form-check">
                                    <input value="6" class="pay-list form-check-input" type="checkbox" name="pay_method" id="defaultCheck1">
                                    <label class="form-check-label" for="defaultCheck1">
                                        Pay with cash at Hotel
                                    </label> &emsp13;
                                    <img src="{{asset('images/cash.png')}}" class="pay-img">
                                </div>
                            </div>
                            {{-- <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input value="7" class="pay-list form-check-input" type="checkbox"
                                            name="pay_method" id="defaultCheck1">
                                        <label class="form-check-label" for="defaultCheck1">
                                            others
                                        </label>
                                    </div>
                                </div> --}}
                            </div>
                            </div>
                            </div>

                            <hr>
                            
                            <h5>Person Information</h5>
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Nationality</label>
                                        <select name="nationality" class="form-control select2">
                                            <option value="1">Myanmar</option>
                                            <option value="2">Foreign</option>
                                        </select>
                                        @error('nationality')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                 

                                <div class="col-md-12">
                                    <label for="">Client User</label>
                                    <select name="client_user" class="form-control select2">
                                        <option value="">-- Please Choose --</option>
                                        @foreach($client_user as $data)
                                        <option value="{{$data->id}}">Name : {{$data->name}} , Phone : {{$data->phone}} , Address :
                                            {{$data->address}}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('client_user')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row my-4">
                                <div class="col-md-12 text-center">
                                    <a href="{{ route('admin.booking.index') }}" class="btn btn-danger mr-3">Cancel</a>
                                    <input type="submit" value="Confirm" class="btn btn-success">
                                </div>
                            </div>
                            </form>
                            </div>
                            </div>
                            </div>
                            </div>

@endsection

@section('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

{!! JsValidator::formRequest('App\Http\Requests\StoreBooking', '#create') !!}

<script>
    $(document).ready(function(){
        $('.checkin_checkout').daterangepicker({
            opens:'right',
            startDate: moment(),
            endDate: moment().add(1, 'days'),
            minDate :  moment(),
            locale: {
            format: 'YYYY-MM-DD'
            }
        });

        $('.checkin_checkout').on('hide.daterangepicker', function(ev, picker) {
                var startDate = picker.startDate.format('YYYY-MM-DD');
                var endDate = picker.endDate.format('YYYY-MM-DD');

                if(startDate==endDate){
                    swal("Checkout date is invalid !", "Please do not select checkin & checkout in same date.", "error");

                }
                $('.check_in_date').val(startDate);
                $('.check_out_date').val(endDate);
        });

        $('.pay-list').on('change', function() {
            $('.pay-list').not(this).prop('checked', false);
        });

        $('.check-list').on('change', function() {
            $('.check-list').not(this).prop('checked', false);
        });


    $(function(){
    $('#roomtype_id').change(function(e){

        $('#extra_bed_qty').empty();
        $('#room_qty').empty()

        var extra_bed_qty=$("#roomtype_id option[value='" + $('#roomtype_id').val() + "']").attr('data-id1');
        var room_qty=$("#roomtype_id option[value='" + $('#roomtype_id').val() + "']").attr('data-id3');

        console.log(extra_bed_qty,room_qty);
        $i=0;
        for(i=0;i <= extra_bed_qty;i++){
        $('#extra_bed_qty').append(`<option value='${i}'>${i}</option>`);
            }
         for(i=0;i <= room_qty;i++){
        $('#room_qty').append(`<option value='${i}'>${i}</option>`);
            }
      
        });
    });

     $('#item_id').change(function(e){
            var item_id =parseInt($('#room_qty').val());
            var extra_bed_qty=$("#roomtype_id option[value='" + $('#roomtype_id').val() + "']").attr('data-id1');
            var guest=$("#roomtype_id option[value='" + $('#roomtype_id').val() + "']").attr('data-id4');

            $i=0;
            if(extra_bed_qty !=0){
            $('#extra_bed_qty').empty();
            var check = (room_qty ) * (parseInt(extra_bed_qty));
            for(i=0;i <= check;i++){
                $('#extra_bed_qty').append(`<option value='${i}'>${i}</option>`);
                    }
            }
        });

    });
</script>
@endsection
