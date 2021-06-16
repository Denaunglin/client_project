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
                <form action="{{ route('admin.roomschedules.store') }}" method="post" id="create">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Room No</label>
                                <select name="room_no" class="form-control" required>
                                    @forelse($roomlayout as $data)
                                    <option value="{{$data->id}}">{{$data->room_no}}</option>
                                    @empty<option>There is no room type</option>
                                    @endforelse

                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Room</label>
                                <select name="room_id" class="form-control" required>
                                    @forelse($room as $data)
                                    <option value="{{$data->id}}">Room Type -
                                        {{$data->roomtype ? $data->roomtype->name : '-'}} | Bed Type -
                                        {{$data->bedtype ? $data->bedtype->name : '-'}}</option>
                                    @empty<option>There is no room type</option>
                                    @endforelse

                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>CheckIn - CheckOut</label>
                                <input type="text" id="checkin_checkout" name="checkin_checkout" class="form-control"
                                    required>
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

</script>

<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
{!! JsValidator::formRequest('App\Http\Requests\RoomScheduleRequest', '#create') !!}
@endsection
