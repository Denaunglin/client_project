@extends('backend.admin.layouts.app')
@section('meta_title', 'Booking Information & Status Update')
@section('page_title', 'Booking Information & Status Update')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('style')
<style>
    table td:nth-of-type(1) {
        width: 50%;
    }
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-md-6  mb-3">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <h5>Booking Information</h5>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>Booking Number</td>
                                <td>{{$booking->booking_number}}</td>
                            </tr>
                            @if($booking->roomschedule)
                            <tr>
                                <td>Taken Room No</td>
                                <td>
                                @foreach($takeroom as $takerooms)
                                {{$takerooms->roomlayout->room_no}} ,
                                @endforeach
                                </td>
                            </tr>
                            @endif
                            <tr>
                                <td> Nationality</td>
                                <td>{{$nationality[$booking->nationality]}}</td>
                            </tr>
                            <tr>
                                <td>Room Type</td>
                                <td>
                                    @if($booking->room)
                                    {{$booking->room->roomtype ? $booking->room->roomtype->name : 'Room type not found'}}
                                    @else
                                    Room not found
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>Bed Type</td>
                                @if($booking->room)
                                <td>{{$booking->room->bedtype ? $booking->room->bedtype->name :"Bed Type not found "}}
                                </td>
                                @endif
                            </tr>
                            <tr>
                                <td>Checkin - Checkout</td>
                                <td>{{$booking->check_in}} - {{$booking->check_out}}</td>
                            </tr>
                            <tr>
                                <td>Room Qty</td>
                                <td>{{$booking->room_qty}} Room</td>
                            </tr>
                            <tr>
                                <td>Guest</td>
                                <td>{{$booking->guest}} Person</td>
                            </tr>
                            <tr>
                                <td>Payment method</td>
                                <td>{{$pay_method[$booking->pay_method]}}</td>
                            </tr>

                             @if($booking->early_checkin_time)
                            <tr>
                                <td>CheckIn Time</td>
                                <td> {{$booking->early_checkin_time}}</td>
                            </tr>
                            @endif

                            @if($booking->late_checkout_time)
                            <tr>
                                <td>CheckOut Time</td>
                                <td>{{$booking->late_checkout_time}}</td>
                            </tr>
                            @endif

                            <tr>
                                <td colspan="2" class="bg-light"></td>
                            </tr>

                            @if($booking->other_services)
                            <tr>
                                <td>Other Services Name </td>
                                <td>
                                    @foreach($otherservicesdata as $data)
                                    {{$data['name']}}({{$data['qty']}}) ,
                                    @endforeach
                                </td>
                            </tr>

                            <tr>
                                <td>Other Services Charges</td>
                                @if($booking->nationality==1)
                                <td>
                                    @foreach($otherservicesdata as $data)
                                    {{$sign1}} {{$data['charges'] *  $data['qty']}} {{$sign2}} ,
                                    @endforeach
                                </td>
                                @else
                                <td>
                                    @foreach($otherservicesdata as $data)
                                    {{$sign1}} {{$data['charges'] *  $data['qty']}} {{$sign2}} ,
                                    @endforeach
                                </td>
                                @endif
                            </tr>
                            <tr>
                                <div style="display: none">
                                    {{ $total = 0 }}
                                </div>
                                     @foreach($otherservicesdata as $data)
                                     <div style="display: none">{{$total += $data['total']}}</div>
                                    @endforeach  
                            </tr>
                            
                            <tr>
                                  <td> Other Service Charges total  </td> 
                                  <td> {{$sign1}} {{$total}} {{$sign2}}</td>
                            </tr>

                            <tr>
                                <td>Commercial tax ({{$commercial_percentage }} %) </td>
                            <td>{{$sign1}} {{$total * ($commercial_percentage / 100) }} {{$sign2}}  </td>
                            </tr>

                            <tr>
                                <td>Grand Total</td>
                            <td>{{$sign1}} {{$total + ($total * ($commercial_percentage / 100))}} {{$sign2}}</td>
                            </tr>
                            @endif
                         
                            <tr>
                                <td colspan="2" class="bg-light"></td>
                            </tr>

                            <tr>
                                <td>Price (1 room per night)</td>
                                <td>{{$sign1}} {{$booking->price}} {{$sign2}} </td>
                            </tr>

                            @if($booking->price > $booking->discount_price)
                            <tr>
                                <td> Discount Price (1 room per night)</td>
                                <td>{{$sign1}} {{$booking->discount_price}} {{$sign2}} </td>
                            </tr>
                            @endif

                            <tr>
                                 @php
                                $nights =
                                Carbon\Carbon::parse($booking->check_out)->diffInDays(Carbon\Carbon::parse($booking->check_in));
                                @endphp
                                <td>({{$booking->extra_bed_qty}} Extra Bed x <span>{{$nights}} </span>Nights) </td>
                                <td>{{$sign1}} {{$booking->extra_bed_total}} {{$sign2}}</td>
                            </tr>

                            @if($booking->early_check_in != 0 )

                            <tr>
                                <td>Early Check-In Price </td>
                                <td>{{$sign1}} {{($booking->early_check_in)}} {{$sign2}}</td>
                            </tr>
                            @elseif($booking->late_check_out != 0)

                            <tr>
                                <td>Late Check-Out Price</td>
                                <td>{{$sign1}} {{($booking->late_check_out)}} {{$sign2}}</td>
                            </tr>
                            @elseif($booking->both_check != 0 )
                            <tr>
                                <td>Both Early Check-In & <br> Late Check-Out Price</td>
                                <td>{{$sign1}} {{($booking->both_check)}} {{$sign2}}</td>
                            </tr>
                            @endif

                            <tr>
                                <td>Service Charges ({{$service_percentage}}%)</td>
                                <td>{{$sign1}} {{$booking->service_tax}} {{$sign2}}</td>
                            </tr>

                            <tr>
                                @php
                                $nights =
                                Carbon\Carbon::parse($booking->check_out)->diffInDays(Carbon\Carbon::parse($booking->check_in));
                                @endphp
                                <td>Total
                                    (<span>{{$booking->room_qty}}</span> Room x <span>{{$nights}}
                                    </span>Nights)
                                </td>
                                <td>{{$sign1}}{{$booking->total}} {{$sign2}}</td>
                            </tr>
                            <tr>
                                <td>Commercial Taxes ({{$commercial_percentage}} %)</td>
                                <td>{{$sign1}} {{$booking->commercial_tax}} {{$sign2}}</td>
                            </tr>
                            <tr>
                                <td colspan="2" class="bg-light"></td>
                            </tr>
                            <tr class="bg-warning">
                                <td>Grand Total</td>
                                <td>{{$sign1}}{{$booking->grand_total}} {{$sign2}}</td>
                            </tr>
                            @if($booking->commission==0)

                            @else
                            <tr>
                                <td>Commission ({{$commission_percentage}} %)</td>
                                <td>{{$sign1}} {{$commission}} {{$sign2}}</td>
                            </tr>
                            <tr class="bg-warning">
                                <td>Balance</td>
                                <td>{{$sign1}}{{$booking->grand_total-$commission}} {{$sign2}}</td>
                            </tr>
                            @endif


                        </tbody>
                    </table>
                </div>

                <h5>Customer Information</h5>
                <div class="table-responsive mb-3">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <td>Name</td>
                                <td>{{$booking->name}}

                                    @if($booking->client_user)
                                    ( <span class="text-warning"> {{$booking->member_type}}</span> )
                                    @else
                                    ( <span class="text-warning">Default Member</span> )
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td>Email</td>
                                <td>{{$booking->email}}</td>
                            </tr>
                            <tr>
                                <td>Phone</td>
                                <td>{{$booking->phone}}</td>
                            </tr>

                            @if($booking->credit_type)
                            <tr>
                                <td>Credit Card Type</td>
                                <td>{{$booking->cardtype->name}}</td>
                            </tr>
                            @endif
                            @if($booking->credit_no)
                            <tr>
                                <td>Credit Card Number</td>
                                <td>{{$booking->credit_no}}</td>
                            </tr>
                            @endif
                            @if($booking->credit_no)
                            <tr>
                                <td>Card Expire Date</td>
                                <td>{{$month[$booking->expire_month]}} / {{$year[$booking->expire_year]}}</td>
                            </tr>
                            @endif
                            @if($booking->message)
                            <tr>
                                <td>Message</td>
                                <td>{{$booking->message}}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

            
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        @if($condition2 != '2')
                <div class="main-card mb-3 card">
                    <div class="card-body">
                        @if($booking->status != 1)
                            @if($avaliable_room_qty > 0)
                            <p class="text-danger">Avaliable Room Qty - We have {{$avaliable_room_qty}} left.</p>
                            @else
                            <p class="alert alert-danger">All Room are not avaliable.</p>
                            @endif
                        @endif

                        @if($errors->any())
                            <div class="row">
                                <div class="col-md-12">
                                    @foreach ($errors->all() as $error)
                                    <div class="alert alert-danger" role="alert">
                                        <i class="fas fa-info-circle"></i> {{$error}}
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <form method="post" action="{{ route('admin.booking.update',[$booking->id]) }}">
                            @csrf
                            @method('PUT')
                            <h5>Status Information</h5>
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control select2" name="status">
                                    {!!App\Helper\HelperFunction::statusOptions($booking->status)!!}
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Payment Status</label>
                                <select class="form-control select2" name="payment_status">
                                    {!!App\Helper\HelperFunction::paymentStatusOptions($booking->payment_status)!!}
                                </select>
                            </div>

                                @if($condition1 == '1')
                                    <div style="display: none">
                                            <h5>Room Information</h5>
                                            <div class="form-group">
                                                <label>Please Choose Room Number</label>
                                                <select class="form-control select2" name="room_no[]" id="roles" multiple required>
                                                    <option value="">-- Please Choose --</option>
                                                    @foreach($roomno as $data)
                                                    <option value="{{$data->id}}" 
                                                    @if($booking->roomschedule) @if($data->id ==
                                                        $booking->roomschedule->room_no) selected @endif @endif
                                                        >{{$data->room_no}}
                                                        @foreach($check_room as $check)
                                                        @if($check->roomlayout->room_no==$data->room_no) <p class="text-danger"> &emsp13;
                                                            (Already Taken !)</p> @endif
                                                        @endforeach
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <h5>Checkin Checkout Information</h5>
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input value="1" @if($booking->early_check_in != 0 ) checked @elseif($booking->both_check != 0 )
                                                    checked @endif class="form-check-input" type="checkbox" name="early_late[]"
                                                    id="defaultCheck1">
                                                    <label class="form-check-label" for="defaultCheck1">
                                                        Early Check-In
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input value="2" @if($booking->late_check_out != 0) checked @elseif($booking->both_check != 0)
                                                    checked @endif class="form-check-input" type="checkbox" name="early_late[]"
                                                    id="defaultCheck1">
                                                    <label class="form-check-label" for="defaultCheck1">
                                                        Late Check-Out
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label>Check-In Time</label>
                                                <input type="time" value="{{$booking->early_checkin_time}}" class="form-control timepicker"
                                                    name="early_checkin_time">
                                            </div>
                                            <div class="form-group">
                                                <label>Check-Out Time</label>
                                                <input type="time" value="{{$booking->late_checkout_time}}" class="form-control timepicker"
                                                    name="late_checkout_time">
                                            </div>
                                            <h5>Other Service</h5>
                                            <div class="form-group">
                                                @foreach($otherservicescategory as $data)
                                                <label> <b> {{$data->name}} </b></label>
                                                <div class="row">
                                                    @forelse($otherservicesitem as $item)
                                                    @if($item->other_services_category_id==$data->id)
                                                        <div class="col-md-6">
                                                        <label>{{$item->name}}</label>
                                                        <input type="checkbox" @if($booking->other_services)
                                                        @foreach($otherservicesdata as $check)
                                                        @if($check['id']==$item->id)
                                                        checked
                                                        @endif
                                                        @endforeach
                                                        @endif
                                                        name="other_services[]" id={{$item->id}}  value="{{$item->id}}" >
                                                        &ensp; 
                                                    
                                                        <select class="float-right"  name="other_service_qty_{{$item->id}}" >
                                                            <option  value="0">0</option>
                                                            <option  
                                                            @if($booking->other_services)
                                                            @foreach($otherservicesdata as $check)
                                                            @if($check['qty']=='1')
                                                            @if($check['id']==$item->id)
                                                            selected
                                                            @endif
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            value="1">1</option>
                                                            
                                                            <option 
                                                            @if($booking->other_services)
                                                            @foreach($otherservicesdata as $check)
                                                            @if($check['qty']=='2')
                                                            @if($check['id']==$item->id)
                                                            selected
                                                            @endif
                                                            @endif
                                                            @endforeach 
                                                            @endif
                                                            value="2">2</option>

                                                            <option  
                                                            @if($booking->other_services)
                                                            @foreach($otherservicesdata as $check)
                                                            @if($check['qty']=='3')
                                                            @if($check['id']==$item->id)
                                                            selected
                                                            @endif
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            value="3">3</option>

                                                            <option
                                                            @if($booking->other_services)
                                                            @foreach($otherservicesdata as $check) 
                                                            @if($check['qty']=='4')
                                                            @if($check['id']==$item->id)
                                                            selected
                                                            @endif
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            value="4">4</option>
                                                        
                                                            <option  
                                                            @if($booking->other_services)
                                                            @foreach($otherservicesdata as $check)
                                                            @if($check['qty']=='5')
                                                            @if($check['id']==$item->id)
                                                            selected
                                                            @endif
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            value="5">5</option>

                                                            <option 
                                                            @if($booking->other_services)
                                                            @foreach($otherservicesdata as $check)
                                                            @if($check['qty']=='6')
                                                            @if($check['id']==$item->id)
                                                            selected
                                                            @endif
                                                            @endif
                                                            @endforeach 
                                                            @endif
                                                            value="6">6</option>

                                                            <option 
                                                            @if($booking->other_services)
                                                            @foreach($otherservicesdata as $check)
                                                            @if($check['qty']=='7')
                                                            @if($check['id']==$item->id)
                                                            selected
                                                            @endif
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            value="7">7</option>
                                                            
                                                            <option 
                                                            @if($booking->other_services)
                                                            @foreach($otherservicesdata as $check)
                                                            @if($check['qty']=='8')
                                                            @if($check['id']==$item->id)
                                                            selected
                                                            @endif
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            value="8">8</option>

                                                            <option  
                                                            @if($booking->other_services)
                                                            @foreach($otherservicesdata as $check)
                                                            @if($check['qty']=='9')
                                                            @if($check['id']==$item->id)
                                                            selected
                                                            @endif
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            value="9">9</option>

                                                            <option 
                                                            @if($booking->other_services)
                                                            @foreach($otherservicesdata as $check) 
                                                            @if($check['qty']=='10')
                                                            @if($check['id']==$item->id)
                                                            selected
                                                            @endif
                                                            @endif
                                                            @endforeach
                                                            @endif
                                                            value="10">10</option>
                                                        </select>
                                                        </div>
                                                        @endif

                                                        @empty
                                                    @endforelse
                                                </div>
                                                @endforeach
                                            </div>
                                    </div>
                                @else

                                    <h5>Room Information</h5>
                                    <div class="form-group">
                                        <label>Please Choose Room Number</label>
                                        <select class="form-control select2" name="room_no[]" multiple required>
                                            <option value="">-- Please Choose --</option>
                                                @foreach($roomno as $data)
                                                        <option value="{{$data->id}}"
                                                        @foreach($takeroom as $aa)
                                                        @if($booking->roomschedule) @if($data->id ==
                                                            $aa->roomlayout->id) selected @endif @endif
                                                        @endforeach   
                                                            > {{$data->room_no}}
                                                        @foreach($check_room as $check)
                                                        @if($check->roomlayout->room_no==$data->room_no) <p class="text-danger"> &emsp13;
                                                            (Already Taken !)</p> @endif    
                                                        @endforeach
                                                        
                                                    </option>
                                                @endforeach
                                        </select>
                                    </div>
                                    <h5>Checkin Checkout Information</h5>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input value="1" @if($booking->early_check_in != 0) checked @elseif($booking->both_check != 0)
                                            checked @endif class="form-check-input" type="checkbox" name="early_late[]"
                                            id="defaultCheck1">
                                            <label class="form-check-label" for="defaultCheck1">
                                                Early Check-In
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input value="2" @if($booking->late_check_out != 0) checked @elseif($booking->both_check != 0)
                                            checked @endif class="form-check-input" type="checkbox" name="early_late[]"
                                            id="defaultCheck1">
                                            <label class="form-check-label" for="defaultCheck1">
                                                Late Check-Out
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Check-In Time</label>
                                        <input type="time" value="{{$booking->early_checkin_time}}" class="form-control timepicker"
                                            name="early_checkin_time">
                                    </div>
                                    <div class="form-group">
                                        <label>Check-Out Time</label>
                                        <input type="time" value="{{$booking->late_checkout_time}}" class="form-control timepicker"
                                            name="late_checkout_time">
                                    </div>

                                    <div class="form-group">
                                        <label>Deposite Amount</label>
                                    <input type="number" step="any" value="{{$booking->deposite}}" name="deposite" class="form-control" >
                                    </div>


                                    <h5>Other Service</h5>
                                    <div class="form-group">
                                        @foreach($otherservicescategory as $data)
                                        <label> <b> {{$data->name}} </b></label>
                                        <div class="row">
                                            @forelse($otherservicesitem as $item)
                                            @if($item->other_services_category_id==$data->id)
                                            <div class="col-md-6">
                                                <label>{{$item->name}}</label>
                                                <input type="checkbox" @if($booking->other_services)
                                                @foreach($otherservicesdata as $check)
                                                @if($check['id']==$item->id)
                                                checked
                                                @endif
                                                @endforeach
                                                @endif
                                                name="other_services[]" id={{$item->id}}  value="{{$item->id}}" >
                                                &ensp; 
                                            
                                                <select class="float-right"  name="other_service_qty_{{$item->id}}" >
                                                    <option  value="0">0</option>
                                                    <option  
                                                    @if($booking->other_services)
                                                    @foreach($otherservicesdata as $check)
                                                    @if($check['qty']=='1')
                                                    @if($check['id']==$item->id)
                                                    selected
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                    value="1">1</option>
                                                    
                                                    <option 
                                                    @if($booking->other_services)
                                                    @foreach($otherservicesdata as $check)
                                                    @if($check['qty']=='2')
                                                    @if($check['id']==$item->id)
                                                    selected
                                                    @endif
                                                    @endif
                                                    @endforeach 
                                                    @endif
                                                    value="2">2</option>

                                                    <option  
                                                    @if($booking->other_services)
                                                    @foreach($otherservicesdata as $check)
                                                    @if($check['qty']=='3')
                                                    @if($check['id']==$item->id)
                                                    selected
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                    value="3">3</option>

                                                    <option
                                                    @if($booking->other_services)
                                                    @foreach($otherservicesdata as $check) 
                                                    @if($check['qty']=='4')
                                                    @if($check['id']==$item->id)
                                                    selected
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                    value="4">4</option>
                                                
                                                    <option  
                                                    @if($booking->other_services)
                                                    @foreach($otherservicesdata as $check)
                                                    @if($check['qty']=='5')
                                                    @if($check['id']==$item->id)
                                                    selected
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                    value="5">5</option>

                                                    <option 
                                                    @if($booking->other_services)
                                                    @foreach($otherservicesdata as $check)
                                                    @if($check['qty']=='6')
                                                    @if($check['id']==$item->id)
                                                    selected
                                                    @endif
                                                    @endif
                                                    @endforeach 
                                                    @endif
                                                    value="6">6</option>

                                                    <option 
                                                    @if($booking->other_services)
                                                    @foreach($otherservicesdata as $check)
                                                    @if($check['qty']=='7')
                                                    @if($check['id']==$item->id)
                                                    selected
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                    value="7">7</option>
                                                    
                                                    <option 
                                                    @if($booking->other_services)
                                                    @foreach($otherservicesdata as $check)
                                                    @if($check['qty']=='8')
                                                    @if($check['id']==$item->id)
                                                    selected
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                    value="8">8</option>

                                                    <option  
                                                    @if($booking->other_services)
                                                    @foreach($otherservicesdata as $check)
                                                    @if($check['qty']=='9')
                                                    @if($check['id']==$item->id)
                                                    selected
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                    value="9">9</option>

                                                    <option 
                                                    @if($booking->other_services)
                                                    @foreach($otherservicesdata as $check) 
                                                    @if($check['qty']=='10')
                                                    @if($check['id']==$item->id)
                                                    selected
                                                    @endif
                                                    @endif
                                                    @endforeach
                                                    @endif
                                                    value="10">10</option>
                                                </select>
                                            </div>
                                            @endif

                                            @empty
                                            @endforelse
                                        </div>
                                        @endforeach
                                    </div>

                                @endif
                            
                            @if($booking->cancellation)
                                <p class="text-danger text-center "> Booking Canceled !</p>
                            @else
                                <div class="form-group mt-3">
                                    <button class="btn btn-success btn-block" type="submit">Update</button>
                                </div>
                            @endif

                        </form>

                    </div>
                </div>

                @if($booking->cancellation)
                    <div class="col-md-12 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title text-center"> Booking Cancellation remark</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                            <td>Remark</td>
                                            <td>{{$booking->cancellation_remark}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
        @endif
    </div>
</div>
<hr>

<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item waves-effect waves-light">
            <a class="nav-link text-dark @if(Session('success')) @else active  @endif " id="invoice-tab" data-toggle="tab" href="#invoice" role="tab" aria-controls="invoice" aria-selected="true">Invoice</a>
    </li>
    <li class="nav-item waves-effect waves-light">
            <a class="nav-link text-dark" id="extrainvoice-tab" data-toggle="tab" href="#extrainvoice" role="tab" aria-controls="extrainvoice" aria-selected="false">Extra Invoice</a>
    </li>
    <li class="nav-item waves-effect waves-light">
            <a class="nav-link text-dark @if(Session('success')) active  @endif " id="payslip-tab" data-toggle="tab" href="#payslip" role="tab" aria-controls="payslip" aria-selected="false">Payslip</a>
    </li>
     <li class="nav-item waves-effect waves-light">
            <a class="nav-link text-dark " id="payment_information-tab" data-toggle="tab" href="#payment_information" role="tab" aria-controls="payment_information" aria-selected="false">Payment Information</a>
    </li>
</ul>

 <div class="tab-content" id="myTabContent">

    <div class="tab-pane mt-4 fade @if(Session('success')) @else active show  @endif " id="invoice" role="tabpanel" aria-labelledby="invoice-tab">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Invoice</h5>
                    <a href="{{route('admin.admin_invoice_pdf', $booking->id)}}" class="btn btn-success mb-3">Make &
                        Download
                        Invoice</a>

                    <h5 class="card-title">Invoice History</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Invoice Number</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th class="text-center">Action</th>
                            </tr>
                            <tbody>
                                @forelse($invoice as $data)
                                <tr>
                                    <td><a href="{{$data->pdf_path()}}">{{$data->invoice_no}}</a></td>
                                    <td>{{$data->created_at}}</td>
                                    <td>{{$data->updated_at}}</td>
                                    <td class="text-center"><a href="{{$data->pdf_path()}}"><i class="fas fa-info-circle"></i></a></td>
                                </tr>
                                @empty
                                <p class="alert alert-danger">Invoice Not Found</p>
                                @endforelse
                            </tbody>
                        </table>
                        {{$invoice->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane mt-4 fade" id="extrainvoice" role="tabpanel" aria-labelledby="extrainvoice-tab">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    @if($booking->other_services)

                    <h5 class="card-title">Extra Invoice</h5>
                    <a href="{{route('admin.admin_extra_invoice_pdf', $booking->id)}}" class="btn btn-success mb-3">Make &
                        Download
                        Extra Invoice</a>
                    @endif

                    <h5 class="card-title">Extra Invoice History</h5>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>Invoice Number</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th class="text-center">Action</th>
                            </tr>
                            <tbody>
                                @forelse($extrainvoice as $data)
                                <tr>
                                    <td><a href="{{$data->pdf_path()}}">{{$data->invoice_no}}</a></td>
                                    <td>{{$data->created_at}}</td>
                                    <td>{{$data->updated_at}}</td>
                                    <td class="text-center"><a href="{{$data->pdf_path()}}"><i class="fas fa-info-circle"></i></a></td>
                                </tr>
                                @empty
                                <p class="alert alert-danger">Extra Invoice Not Found</p>
                                @endforelse
                            </tbody>
                        </table>
                        {{$extrainvoice->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane mt-4 fade @if(Session('success')) active show @endif " id="payslip" role="tabpanel" aria-labelledby="payslip-tab">
             
                <div class="container">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{route('payslip_post')}}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Payslip Image</label>
                                    <input type="hidden" name="booking_id" id="booking_id" value="{{$booking->id}}">
                                    <input type="file" name="payslip_image" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Remark</label>
                                <textarea name="remark" class="form-control" cols="30" rows="3  ">@if($payslip) {{$payslip->remark}} @endif</textarea>
                                </div>
                                <div class="form-group ">
                                    <button class="btn btn-success btn-block" type="submit">Upload</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                    <div class="col-md-12">
                                        <div class="table-responsive">
                                            <table class="align-middle mb-0 table data-table" style="width:100%; " id="images">
                                                <thead>
                                                    <th class="no-sort" >Booking Payslip</th>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                
    </div>

     <div class="tab-pane mt-4 fade  " id="payment_information" role="tabpanel" aria-labelledby="payment_information-tab">
            <div class="card center-elem">
            <div class="card-header">
                Payment Infromation
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="table ">
                        <table>
                            @if($booking->nationality == 1)
                                <tr>
                                    <td>Room Total Amount</td>
                                    <td>{{$booking->grand_total - $commission}} MMK</td>
                                </tr>
                                <tr>
                                    <td>Other Sevices Amount</td>
                                    <td> {{$booking->other_charges_total + ($booking->other_charges_total * ($commercial_percentage/100) ) }} MMK </td>
                                </tr>
                                <tr>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <td>Total Amount</td>
                                    <td> {{($booking->other_charges_total + ($booking->other_charges_total * ($commercial_percentage/100) )) + ($booking->grand_total - $commission) }} MMK </td>
                                </tr>
                                 <tr>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <td>Deposite Amount</td>
                                    <td>{{ $booking->deposite}} MMK</td>
                                </tr>
                                 <tr>
                                    <td colspan="2"></td>
                                </tr>
                                <tr class="bg-warning">
                                    <td >Balance Amount</td>
                                    <td>{{((($booking->grand_total-$commission) + $booking->other_charges_total) - $booking->deposite )+ ($booking->other_charges_total * ($commercial_percentage/100))}} MMK</td>
                                </tr>

                            @else
                                <tr>
                                    <td>Room Total Amount</td>
                                    <td>$ {{$booking->grand_total-$commission}} </td>
                                </tr>
                                <tr>
                                    <td>Other Sevices Amount</td>
                                    <td> $ {{$booking->other_charges_total + ($booking->other_charges_total * ($commercial_percentage/100) ) }}  </td>
                                </tr>
                                 <tr>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <td>Total Amount</td>
                                    <td> $ {{($booking->grand_total-$commission) + ($booking->other_charges_total + ($booking->other_charges_total * ($commercial_percentage/100) ) )}}  </td>
                                </tr>
                                 <tr>
                                    <td colspan="2"></td>
                                </tr>
                                <tr>
                                    <td>Deposite Amount</td>
                                    <td> $ {{ $booking->deposite}} </td>
                                </tr>
                                 <tr>
                                    <td colspan="2"></td>
                                </tr>
                                <tr class="bg-warning">
                                    <td >Balance Amount</td>
                                    <td> $ {{((($booking->grand_total-$commission) + $booking->other_charges_total) - $booking->deposite )+ ($booking->other_charges_total * ($commercial_percentage/100))}} </td>
                                </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
{!! JsValidator::formRequest('App\Http\Requests\BookingUpdate', '#update') !!}
<script>
    $(document).ready(function() {
	var max_fields      = 10; //maximum input boxes allowed
	var wrapper   		= $(".input_fields_wrap"); //Fields wrapper
	var add_button      = $(".add_field_button"); //Add button ID

	var x = 1; //initlal text box count
	$(add_button).click(function(e){ //on add input button click
		e.preventDefault();
		if(x < max_fields){ //max input box allowed
			x++; //text box increment
			$(wrapper).append(' <div class="parent row mb-5"><div class="col-md-5"><label>Service Name</label><input type="text" class="form-control" name="service_name[]"></div><div class="col-md-5"><label>Charges</label><input type="number" step="any" class="form-control" name="charges[]"></div><a href="#" id="remove" class="text-danger remove_field" > Remove</a></div>'); //add input box
		}
	});

	$(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});

  $('.check-list').on('change', function() {
        $('.check-list').not(this).prop('checked', false);

    });
</script>
<script>
    $(document).ready(function() {
  $('.image-link').magnificPopup({type:'image'});

});

</script>
 <script>
            
    $(document).ready(function(){
    var viewer = new Viewer(document.getElementById('images'));
    var id= $("#booking_id").val();
    var payslip_table = $(".data-table").DataTable({
            processing: true,
            searching : false,
            serverSide: true,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'refresh'
                }
            ],
            ajax: `/customer/payslips/datatable/ssd/` + id ,
            columns: [
                        {data: 'widget', name: 'widget', defaultContent: "-"}
                    ],
            order: [
                [0, "desc"]
            ],
            columnDefs: [{
                    targets: "no-sort",
                    orderable: true
                },
                {
                    targets: "hidden",
                    visible: false
                }
            ],

            pagingType: "simple_numbers",
                language: {
                    paginate: {
                        previous: "«",
                        next: "»"
                    },
                    processing: `<div class="processing_data">
                        <div class="spinner-border text-success" role="status">
                            <span class="sr-only">Loading...</span>
                        </div></div>`
                },
                    drawCallback: function(){
            viewer.destroy();
            viewer = new Viewer(document.getElementById('images'));
        }
        });

    });

</script>

@include('backend.admin.layouts.assets.trash_script')
@endsection
