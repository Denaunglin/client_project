@extends('backend.admin.layouts.app')

@section('meta_title', 'Add Notification')
@section('page_title', 'Add Notification')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.sendnotifications.store') }}" method="post" id="create">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Send to</label>
                                <select name="user_id[]" class="form-control select2" id="selectbox1" multiple>
                                    @foreach($user as $data)
                                        <option  value="{{$data->id}}">{{$data->name}} / {{$data->email}} / {{$data->phone}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" id="title" name="title" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="description" class="form-control" id="description" cols="10" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>link</label>
                                <input type="text" name="link" id="link" class="form-control">
                            </div>
                        </div>
                     
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.sendnotifications.index') }}" class="btn btn-danger mr-3">Cancel</a>
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
{!! JsValidator::formRequest('App\Http\Requests\sendNotifications', '#create') !!}
@endsection
