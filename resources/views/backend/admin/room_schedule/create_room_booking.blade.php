@extends('backend.admin.layouts.app')

@section('meta_title', 'Add Room Schedule')
@section('page_title', 'Add Room Schedule')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.roomschedules_booking.store') }}" method="post" id="create">
                    @csrf
                    <input type="hidden" name="room_no" value="{{$roomlayout->id}}">

                    <div class="row mb-3">
                        <div class="col-12 mb-3">
                            <label for="">Room Information</label>
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
                                <label>Existing Booking</label>
                                <select name="booking_id" class="form-control select2" required>
                                    @foreach($bookings as $data)
                                        @if(count($data->roomscheduledata) < $data->room_qty)
                                            <option value="{{$data->id}}">Booking Number : {{$data->booking_number}}
                                                (Room Type : {{$data->room->roomtype->name}} , Bed Type :
                                                {{$data->room->bedtype->name}} ) </option>
                                        @endif
                                        @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row my-3">
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
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
{!! JsValidator::formRequest('App\Http\Requests\RoomScheduleRequest', '#create') !!}
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
</script>
@endsection
