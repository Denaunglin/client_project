@extends('backend.admin.layouts.app')

@section('meta_title', 'Add Room Layout')
@section('page_title', 'Add Room Layout')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.roomlayouts.store') }}" method="post" id="create">
                    @csrf
                    <div class="row">
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
                                <label>Room No</label>
                                <input type="text" id="room_no" name="room_no" class="form-control" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Floor</label>
                                <select class="form-control" name="floor">
                                    @foreach($floor as $key => $data)
                                    <option value="{{$key}}">
                                        {{$data}}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Rank</label>
                                <select class="form-control" name="rank">
                                    @foreach($rank as $key => $data)
                                    <option value="{{$key}}">
                                        {{$data}}
                                    </option>
                                    @endforeach
                                </select>
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
{!! JsValidator::formRequest('App\Http\Requests\RoomLayoutRequest', '#create') !!}
@endsection
