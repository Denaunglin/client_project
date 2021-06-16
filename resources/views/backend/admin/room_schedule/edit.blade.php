@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit Room Schedule')
@section('page_title', 'Edit Room Schedule')
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
    <div class="col-md-8">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.roomschedules.update',[$roomschedule->id]) }}" method="post" id="form">
                    @csrf
                    @method('PUT')
                    <h5>Room Information</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Room Status</label>

                                <select name="status" class="form-control">
                                    <option value="1" @if($roomschedule->status == 1) selected @endif>Taken</option>
                                    <option value="2" @if($roomschedule->status == 2) selected @endif>Checkin</option>
                                    <option value="3" @if($roomschedule->status == 3) selected @endif>Checkout (No Cleaning)</option>
                                    <option value="5" @if($roomschedule->status == 5) selected @endif>Clean</option>
                                    <option value="4" @if($roomschedule->status == 4) selected @endif>Cancel</option>
                                </select>
    
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Room No</label>
                                <select name="room_no" class="form-control" required>
                                    <option value="{{$roomschedule->room_no}}">{{$roomschedule->roomlayout->room_no}}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Room</label>
                                <select name="room_id" class="form-control" required>
                                    <option value="{{$roomschedule->room_id}}">Room Type -
                                        {{$roomschedule->room->roomtype ? $roomschedule->room->roomtype->name : '-'}} |
                                        Bed Type -
                                        {{$roomschedule->room->bedtype ? $roomschedule->room->bedtype->name : '-'}}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Guest</label>
                                <select name="guest" class="form-control">
                                    <option value="1" @if($roomschedule->guest=='1') selected @endif>1</option>
                                    <option value="2" @if($roomschedule->guest=='2') selected @endif>2</option>
                                    <option value="3" @if($roomschedule->guest=='3') selected @endif>3</option>
                                    <option value="4" @if($roomschedule->guest=='4') selected @endif>4</option>
                                    <option value="5" @if($roomschedule->guest=='5') selected @endif>5</option>
                                    <option value="6" @if($roomschedule->guest=='6') selected @endif>6</option>
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
                                <select name="room_qty" class="form-control">
                                    <option value="1">1</option>
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
                                <select name="extra_bed_qty" class="form-control">
                                   @php 
                                    $extra_bed_qty=$roomschedule->room ? $roomschedule->room->extra_bed_qty : 0;
                                    @endphp
                                   @for($i = 0; $i <= $extra_bed_qty ; $i++) <option @if($roomschedule->extra_bed_qty == $i) selected @endif value="{{$i}}"
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

                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input value="1" @if($roomschedule->booking->early_check_in != 0) checked
                                @elseif($roomschedule->booking->both_check != 0) checked @endif class=" form-check-input"
                                type="checkbox" name="early_late[]" id="defaultCheck1">
                                <label class="form-check-label" for="defaultCheck1">
                                    Early Check-In
                                </label>
                            </div>
                        </div>

                        <div class="col-12 mb-5">
                            <div class="form-check">
                                <input value="2" @if($roomschedule->booking->late_check_out != 0) checked
                                @elseif($roomschedule->booking->both_check != 0) checked @endif class="form-check-input"
                                type="checkbox" name="early_late[]" id="defaultCheck1">
                                <label class="form-check-label" for="defaultCheck1">
                                    Late Check-Out
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label> Check-In Time</label>
                            <input value="{{$roomschedule->booking->early_checkin_time}}" type="time"
                                class="form-control timepicker" name="early_checkin_time">

                        </div>
                        <div class="col-md-6">
                            <label> Check-Out Time</label>
                            <input value="{{$roomschedule->booking->late_checkout_time}}" type="time"
                                class="form-control timepicker" name="late_checkout_time">
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Nationality</label>
                                <select name="nationality" class="form-control">
                                    <option value="1" @if($roomschedule->nationality=='1') selected @endif>Myanmar
                                    </option>
                                    <option value="2" @if($roomschedule->nationality=='2') selected @endif>Foreign
                                    </option>
                                </select>
                                @error('nationality')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <h5>Payment</h5>
                        </div>
                        <div class="col-md-12 mb-3 mt-3">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input value="6" @if($roomschedule->booking->pay_method==6) checked @endif
                                        class="pay-list form-check-input" type="checkbox" name="pay_method"
                                        id="defaultCheck1">
                                        <label class="form-check-label" for="defaultCheck1">
                                           Pay with cash at Hotel
                                        </label> &emsp13;
                                        <img src="{{asset('images/cash.png')}}" class="pay-img">
                                    </div>
                                </div>
                            </div>
                        </div>
                          <div class="col-md-12 mb-3">
                            <label>Deposite</label>
                          <input type="number" value="{{$roomschedule->booking->deposite}}" name="deposite" class="form-control">
                        </div>
                    </div>

                    <h5>Personal Information</h5>
                    @if($roomschedule->client_user == null)
                    <div class="col-md-12 mb-3">
                                <div class="row">
                                        <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Name</label>
                                        <input type="text" name="name" value="{{$roomschedule->booking->name}}" class="form-control">
                                        </div>
                                    </div>
                                     <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Email</label>
                                        <input type="text" name="email" value="{{$roomschedule->booking->email}}" class="form-control">
                                        </div>
                                    </div>
                                     <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Phone</label>
                                        <input type="number" name="phone" value="{{$roomschedule->booking->phone}}" class="form-control">
                                        </div>
                                    </div>
                                     <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Nrc Passport</label>
                                        <input type="text" name="nrc_passport" value="{{$roomschedule->booking->nrc_passport}}" class="form-control">
                                        </div>
                                    </div>
                                </div>
                        </div>
                    @endif
                    
                    <div class="row mb-3">
                        @if($roomschedule->client_user !== null)
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Client User</label>
                                <select name="client_user" class="form-control">
                                    <option value="">Select User</option>
                                    @forelse($client_user as $data)
                                    <option value="{{$data->id}}" @if($data->id==$roomschedule->client_user) selected
                                        @endif>{{$data->name}} / {{$data->phone}} / {{$data->address}}</option>
                                    @empty
                                    <p>There is no data !</p>
                                    @endforelse
                                </select>
                                @error('client_user')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        @endif
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>CheckIn - CheckOut</label>
                                <input type="text" id="checkin_checkout"
                                    value="{{$roomschedule->check_in}} - {{$roomschedule->check_out}}"
                                    name="checkin_checkout" class="form-control" required>
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.roomlayouts.index') }}" class="btn btn-danger mr-3">Cancel</a>
                            <input type="submit" value="Confirm" class="btn btn-success">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
               <div class="table-responsive">
            <h3>Payment Information</h3>
            <table class="table table-bordered">
              @if($roomschedule->booking->nationality == 1)
                <tr>
                    <td>Room Total Amount</td>
                    <td>{{$roomschedule->booking->grand_total-$commission}} MMK</td>
                </tr>
                <tr>
                    <td>Other Sevices Amount</td>
                    <td> {{$roomschedule->booking->other_charges_total + ($roomschedule->booking->other_charges_total * ($commercial_percentage/100) )}} MMK </td>
                </tr>
                <tr>
                    <td>Total Amount</td>
                    <td>{{($roomschedule->booking->grand_total + $roomschedule->booking->other_charges_total) + ($roomschedule->booking->other_charges_total * ($commercial_percentage/100) ) }} MMK</td>
                </tr>
                <tr>
                    <td>Deposite Amount</td>
                    <td>{{ $roomschedule->booking->deposite}} MMK</td>
                </tr>
                <tr class="bg-warning">
                    <td >Balance Amount</td>
                    <td>{{(($roomschedule->booking->grand_total + $roomschedule->booking->other_charges_total) - $roomschedule->booking->deposite)+ ($roomschedule->booking->other_charges_total * ($commercial_percentage/100) ) }} MMK</td>
                </tr>

              @else
                <tr>
                    <td>Room Total Amount</td>
                    <td> $ {{$roomschedule->booking->grand_total-$commission}} </td>
                </tr>
                <tr>
                    <td>Other Sevices Amount</td>
                    <td> $ {{$roomschedule->booking->other_charges_total + ($roomschedule->booking->other_charges_total * ($commercial_percentage/100) )}}  </td>
                </tr>
                <tr>
                    <td>Total Amount</td>
                    <td> $ {{($roomschedule->booking->grand_total + $roomschedule->booking->other_charges_total) + ($roomschedule->booking->other_charges_total * ($commercial_percentage/100) ) }} </td>
                </tr>
                <tr>
                    <td>Deposite Amount</td>
                    <td> $ {{ $roomschedule->booking->deposite}} </td>
                </tr>
                <tr class="bg-warning">
                    <td>Balance Amount</td>
                    <td> $ {{(($roomschedule->booking->grand_total + $roomschedule->booking->other_charges_total) - $roomschedule->booking->deposite)+ ($roomschedule->booking->other_charges_total * ($commercial_percentage/100) ) }} </td>
                </tr>

              @endif
               
            </table>
        </div>   
            </div>
        </div>
      
    </div>
</div>

@endsection
@section('script')
<script>
    $('.pay-list').on('change', function() {
        $('.pay-list').not(this).prop('checked', false);
    });

    $(function() {
        $('#checkin_checkout').daterangepicker({

                opens:'right',

                locale: {
                format: 'YYYY-MM-DD'
                }
            });
});

</script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
{!! JsValidator::formRequest('App\Http\Requests\RoomScheduleUpdateRequest', '#form') !!}
@endsection
