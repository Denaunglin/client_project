@extends('backend.admin.layouts.app')

@section('meta_title', ' Room Schedule Detail')
@section('page_title', ' Room Schedule Detail')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="text-center">Room Information</h5>
                <p class="card-text">
                    <div class="table-responsive ">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <td>Room No</td>
                                    <td>{{$room_schedule->roomlayout ? $room_schedule->roomlayout->room_no : "-"}}</td>
                                </tr>
                                <tr>
                                    <td>Room Type</td>
                                    <td>{{$room_schedule->room->roomtype ? $room_schedule->room->roomtype->name : '-'}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Bed Type</td>
                                    <td>{{$room_schedule->room->bedtype ? $room_schedule->room->bedtype->name : '-'}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Room Qty</td>
                                    <td>{{$room_schedule->room_qty}}</td>
                                </tr>
                                <tr>
                                    <td>Guest</td>
                                    <td>{{$room_schedule->guest}}</td>
                                </tr>

                                @if($room_schedule->booking->other_services)
                                <tr>
                                    <td>Other Services Name </td>
                                    <td>
                                        @forelse($otherservicesdata as $data)
                                        {{$data->name}} ,
                                        @empty
                                    <td>No data</td>
                                    @endforelse
                                    </td>
                                </tr>

                                <tr>
                                    <td>Other Services Charges</td>
                                    <td>
                                        @if($room_schedule->booking->nationality==1)
                                        @foreach($otherservicesdata as $data)
                                        {{$sign1}} {{$data['charges_mm']}} {{$sign2}} ,
                                        @endforeach
                                        @else
                                        @foreach($otherservicesdata as $data)
                                        {{$sign1}} {{$data['charges_foreign']}} {{$sign2}} ,
                                        @endforeach
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td>Other Service Charges total</td>
                                    <td>{{$sign1}} {{$room_schedule->booking->other_charges_total}} {{$sign2}}</td>
                                </tr>


                                @endif
                                <tr>
                                    <td colspan="2" class="bg-light"></td>
                                </tr>
                                <tr>
                                    <td>Price (1 room per night)</td>
                                    <td> {{$sign1}} {{$room_schedule->booking ? $room_schedule->booking->price  : '-'}} {{$sign2}} </td>
                                </tr>

                                @if($room_schedule->booking->price > $room_schedule->booking->discount_price)
                                <tr>
                                    <td> Discount Price (1 room per night)</td>
                                <td> {{$sign1}} {{$room_schedule->booking->discount_price}} {{$sign2}}</td>
                                @endif

                                <tr>
                                    <td>{{$room_schedule->extra_bed_qty}} Extra Bed</td>
                                    <td> {{$sign1}} {{$room_schedule->booking->extra_bed_total}} {{$sign2}} </td>
                                </tr>

                                @if($room_schedule->booking->early_check_in)

                                <tr>
                                    <td>Early Check-In Price </td>
                                    <td> {{$sign1}} {{($room_schedule->booking->early_check_in)}} {{$sign2}} </td>
                                </tr>
                                @elseif($room_schedule->booking->late_check_out)

                                <tr>
                                    <td>Late Check-Out Price</td>
                                    <td> {{$sign1}} {{($room_schedule->booking->late_check_out)}} {{$sign2}}</td>
                                </tr>
                                @elseif($room_schedule->booking->both_check)
                                <tr>
                                    <td>Both Early/Late Check Price</td>
                                    <td> {{$sign1}} {{($room_schedule->booking->both_check)}} {{$sign2}}</td>
                                </tr>

                                @else

                                @endif


                                @if($room_schedule->booking->early_checkin_time)
                                <tr>
                                    <td>Early CheckIn Time</td>
                                    <td> {{$room_schedule->booking->early_checkin_time}}</td>
                                </tr>
                                @endif

                                @if($room_schedule->booking->late_checkout_time)
                                <tr>
                                    <td>Late CheckOut Time</td>
                                    <td>{{$room_schedule->booking->late_checkout_time}}</td>
                                </tr>
                                @endif

                                <tr>
                                    <td>Services Charges ({{$service_percentage}}%)</td>
                                <td>{{$sign1}} {{$room_schedule->booking->service_tax}} {{$sign2}}</td>
                                </tr>

                                <tr>
                                    <td>Total
                                        (<span>{{$room_schedule->booking->room_qty / $room_schedule->booking->room_qty}}</span> Room x <span>{{$night}}
                                        </span>Nights)
                                    </td>
                                <td> {{$sign1}} {{$room_schedule->booking->total / $room_schedule->booking->room_qty}} {{$sign2}} </td>
                                 
                                </tr>
                                <tr>
                                    <td>Commercial Taxes ({{$commercial_percentage}} %)</td>
                                <td> {{$sign1}} {{$room_schedule->booking->commercial_tax / $room_schedule->booking->room_qty}} {{$sign2}}</td>
                                    
                                </tr>
                                <tr>
                                    <td colspan="2" class="bg-light"></td>
                                </tr>
                                <tr class="bg-warning">
                                    <td>Grand Total</td>
                                <td>{{$sign1}} {{$room_schedule->booking->grand_total /$room_schedule->booking->room_qty}} {{$sign2}}</td>
                                </tr>
                                @if($commission==0)
                                @else
                                <tr>
                                    <td>Commission ({{$commission_percentage}} %)</td>
                                    <td>{{$sign1}} {{$commission /$room_schedule->booking->room_qty}} {{$sign2}}</td>
                                </tr>
                                <tr class="bg-warning">
                                    <td>Balance</td>
                                    <td>{{$sign1}} {{($room_schedule->booking->grand_total-$commission )/$room_schedule->booking->room_qty}} {{$sign2}}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="text-center">Customer Information</h5>
                <p class="card-text">
                    <div class="table-responsive ">
                        <table class="table table-bordered">
                            <tbody>
                                @if($room_schedule->client_user)
                                <tr>
                                    <td>Name</td>
                                    <td>{{$room_schedule->user->name}} ( <span class="text-warning">
                                            {{$room_schedule->user->accounttype->name}} </span> ) </td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>{{$room_schedule->user->email}}</td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td>{{$room_schedule->user->phone}}</td>
                                </tr>
                                <tr>
                                    <td>Nrc or Passport</td>
                                    <td>{{$room_schedule->user->nrc_passport}}</td>
                                </tr>

                                @else

                                <tr>
                                    <td>Name</td>
                                    <td>{{$room_schedule->booking->name}} ( <span class="text-warning"> Default Member
                                        </span> ) </td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>{{$room_schedule->booking->email}}</td>
                                </tr>
                                <tr>
                                    <td>Phone</td>
                                    <td>{{$room_schedule->booking->phone}}</td>
                                </tr>
                                <tr>
                                    <td>Nrc or Passport</td>
                                    <td>{{$room_schedule->booking->nrc_passport}}</td>
                                </tr>

                                @endif

                                <tr>
                                    <td>Nationality</td>
                                    <td>{{$nationality[$room_schedule->nationality]}}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="bg-light"></td>
                                </tr>

                                @if($room_schedule->booking->pay_method==1)

                                <tr>
                                    <td>Payment</td>
                                    <td>{{$pay_method[$room_schedule->booking->pay_method]}}</td>
                                </tr>

                                <tr>
                                    <td>Credit Type</td>
                                    <td>{{$room_schedule->booking->cardtype->name}}</td>
                                </tr>
                                <tr>
                                    <td>Credit Number</td>
                                    <td>{{$room_schedule->booking->credit_no}}</td>
                                </tr>
                                <tr>
                                    <td>Expire Month</td>
                                    <td>{{$month[$room_schedule->booking->expire_month]}}</td>
                                </tr>
                                <tr>
                                    <td>Expire Year</td>
                                    <td>{{$year[$room_schedule->booking->expire_year]}}</td>
                                </tr>
                                @else

                                <tr>
                                    <td>Payment</td>
                                    <td>{{$pay_method[$room_schedule->booking->pay_method]}}</td>
                                </tr>

                                @endif
                            </tbody>
                        </table>
                    </div>
                </p>
            </div>
        </div>
    </div>

</div>

@endsection

@section('script')
{!! JsValidator::formRequest('App\Http\Requests\RoomScheduleRequest', '#create') !!}
@endsection
