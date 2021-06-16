@extends('backend.admin.layouts.app')

@section('meta_title', 'Payslip Detail')
@section('page_title', 'Payslip Detail')

@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <div id="images" class="mb-3">
                    <div class="row">   
                        <div class="col-md-12 border text-center p-3">
                        <img src="{{$payslip->image_path()}}" alt="" width="50%" height="auto">
                        </div>
                        <div class="col-md-12 text-left mt-3">
                            <div class="mb-2">
                                <strong><i class="fab fa-gg mr-1"></i> Booking Number </strong>
                                <p class="text-muted mt-2">{{$payslip->booking_no}}</p>
                            </div>
                            <hr>
                             <div class="mb-2">
                                <strong><i class="fab fa-gg mr-1"></i> Remark </strong>
                                <p class="text-muted mt-2">{{$payslip->remark}}</p>
                            </div>
                            <hr>
                              <div class="mb-2">
                                <strong><i class="fab fa-gg mr-1"></i> Read at </strong>
                                <p class="text-muted mt-2">{{$payslip->read_at}}</p>
                            </div>
                             <hr>
                              <div class="mb-2">
                                <strong><i class="fab fa-gg mr-1"></i> Created at </strong>
                                <p class="text-muted mt-2">{{$payslip->created_at}}</p>
                            </div>
                            <hr>
                              <div class="mb-2">
                                <strong><i class="fab fa-gg mr-1"></i> Updated at </strong>
                                <p class="text-muted mt-2">{{$payslip->updated_at}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if($booking)
    <div class="col-md-6">
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
    @else
    <div class="col-md-6">
       <div class="card">
           <div class="card-body text-center">
               <h5 class="card-text">There is no booking Information</h5>
           </div>
       </div> 
    </div>
    @endif
</div>

@endsection

@section('script')
<script>
    $(document).ready(function(){
        new Viewer(document.getElementById('images'));
    });
</script>
@endsection
