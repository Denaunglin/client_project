@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit Room Layout')
@section('page_title', 'Edit Room Layout')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.roomlayouts.update',[$roomlayout->id]) }}" method="post" id="form">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Room</label>
                                <select name="room_id" class="form-control">
                                    @forelse($room as $data)
                                    <option value="{{$data->id}}" @if($data->id == $roomlayout->room_id) selected
                                        @endif>Room Type - {{$data->roomtype ? $data->roomtype->name : '-'}} | Bed Type
                                        - {{$data->bedtype ? $data->bedtype->name : '-'}}</option>
                                    @empty<option>There is no room type</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Room No</label>
                                <input type="text" value={{$roomlayout->room_no}} id="room_no" name="room_no"
                                    class="form-control" required>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Floor</label>
                                <select class="form-control" name="floor">
                                    @foreach($floor as $key => $data)
                                    <option value="{{$key}}" @if($key==$roomlayout->floor) selected @endif>
                                        {{$data}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Rank</label>
                                <select class="form-control" name="rank" required>
                                    @foreach($rank as $key => $data)
                                    <option value="{{$key}}" @if($key==$roomlayout->rank) selected @endif>
                                        {{$data}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                                <div class="form-group">
                                    <label>Room Maintain</label>
                                    <select class="form-control" name="maintain" required>
                                            <option value="0" @if($roomlayout->maintain==0) selected @endif>
                                                Open
                                            </option>
                                            <option value="1" @if($roomlayout->maintain==1) selected @endif>
                                                Close
                                            </option>
                                        </select>
                                </div>
                        </div>
                    </div>
                   
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.roomlayouts.index') }}" class="btn btn-danger mr-3">Cancel</a>
                            <input type="submit" value="Update" class="btn btn-success">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
{!! JsValidator::formRequest('App\Http\Requests\RoomLayoutRequest', '#edit') !!}
@endsection
