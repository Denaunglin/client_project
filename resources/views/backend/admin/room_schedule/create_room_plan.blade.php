@extends('backend.admin.layouts.app')

@section('meta_title', 'Add Room Schedule')
@section('page_title', 'Add Room Schedule')
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
                <form action="{{ route('admin.roomschedules.store') }}" method="post" id="form">
                    @csrf

                    <input type="hidden" name="room_no" value="{{$roomlayout->id}}">
                    <input type="hidden" name="room_id" value="{{$roomlayout->room_id}}">

                    <div class="row">
                        <div class="col-12 mb-3">
                            <h5>Room Information</h5>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td>Room Number</td>
                                        <td>{{$roomlayout->room_no}}</td>
                                    </tr>
                                    <tr>
                                        <td>Floor</td>
                                        <td>
                                            @if($roomlayout->floor == 1)
                                            Ground Floor
                                            @elseif($roomlayout->floor == 2)
                                            First Floor
                                            @endif
                                        </td>
                                    </tr>
                                    @if($roomlayout->room)
                                    <tr>
                                        <td>Room Type</td>
                                        <td>{{$roomlayout->room->roomtype ? $roomlayout->room->roomtype->name : '-'}}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Bed Type</td>
                                        <td>{{$roomlayout->room->bedtype ? $roomlayout->room->bedtype->name : '-'}}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">Guest</label>
                                <select name="guest" class="form-control">
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
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
                                    $extra_bed_qty=$roomlayout->room ? $roomlayout->room->extra_bed_qty : 0;
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
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input value="1" class=" form-check-input" type="checkbox" name="early_late[]"
                                    id="defaultCheck1">
                                <label class="form-check-label" for="defaultCheck1">
                               Early  Check-In
                                </label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input value="2" class="form-check-input" type="checkbox" name="early_late[]"
                                    id="defaultCheck1">
                                <label class="form-check-label" for="defaultCheck1">
                                Late Check-Out
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label>Check-In Time</label>
                            <input type="time" class="form-control timepicker" name="early_checkin_time">

                        </div>
                        <div class="col-md-6">
                            <label>Check-Out Time</label>
                            <input type="time" class="form-control timepicker" name="late_checkout_time">
                        </div>
                    </div>

                    <hr>

                    <h5>Payment</h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <div class="row">
                                
                                <div class="col-md-4 mb-3">
                                    <div class="form-check">
                                        <input value="6" class="pay-list form-check-input" required type="checkbox" name="pay_method"
                                            id="defaultCheck1">
                                        <label class="form-check-label" for="defaultCheck1">
                                            Pay with cash at Hotel
                                        </label> &emsp13;
                                        <img src="{{asset('images/cash.png')}}" class="pay-img">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <label>Deposite</label>
                            <input type="number" name="deposite" class="form-control">
                        </div>
                    </div>

                        <hr>

                        <h5>Personal Information</h5>
                        <div class="col-md-12 mb-3">
                            <input value="1" name="registered" type="radio" data-toggle="collapse"
                                href="#collapseExample" role="button" aria-expanded="false"
                                aria-controls="collapseExample" class="registered form_control"> Account Already Regesitered ?
                            
                            <div class="collapse" id="collapseExample">
                                <div class="row">
                                   <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Client User</label>
                                            <select name="client_user" class="form-control">
                                                <option value="">Select User</option>
                                                @forelse($client_user as $data)
                                                <option value="{{$data->id}}">{{$data->name}} / {{$data->phone}} / {{$data->address}}</option>
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
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                         <input value="2"  name="registered" type="radio" data-toggle="collapse"
                            href="#collapseExample1" role="button" aria-expanded="false"
                            aria-controls="collapseExample1" class="registered form_control"> Do not have an Account ? 
                        
                            <div class="collapse" id="collapseExample1">
                                <div class="row">
                                        <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Name</label>
                                            <input type="text" name="name" class="form-control">
                                        </div>
                                    </div>
                                     <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Email</label>
                                            <input type="text" name="email" class="form-control">
                                        </div>
                                    </div>
                                     <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Phone</label>
                                            <input type="number" name="phone" class="form-control">
                                        </div>
                                    </div>
                                     <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="">Nrc Passport</label>
                                            <input type="text" name="nrc_passport" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="">Nationality</label>
                                    <select name="nationality" class="form-control">
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
                                <div class="form-group">
                                    <label>CheckIn - CheckOut</label>
                                    <input type="text" id="checkin_checkout" name="checkin_checkout" class="form-control" required>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <a href="{{ route('admin.roomplan') }}" class="btn btn-danger mr-3">Cancel</a>
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
<script>
    $(function() {
        $('#checkin_checkout').daterangepicker({
            opens:'right',
                startDate: moment(),
                endDate: moment().add(1, 'days'),
                minDate :  moment(),
                locale: {
                format: 'YYYY-MM-DD'
                }
            });
});

    $('.pay-list').on('change', function() {
        $('.pay-list').not(this).prop('checked', false);
    });

    $('.registered').on('change', function() {
        $('.registered').not(this).prop('checked', false);
    });

    $('.check-list').on('change', function() {
        $('.check-list').not(this).prop('checked', false);
    });

</script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
{!! JsValidator::formRequest('App\Http\Requests\RoomScheduleRequest','#form') !!}
@endsection
