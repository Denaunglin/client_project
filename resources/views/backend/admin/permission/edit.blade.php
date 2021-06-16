@extends('backend.admin.layouts.app')
@section('title','Edit Permission')

@php
if(request('guard') == 'admin') {
  $name = 'Admin';
} else {
  $name = 'Customer';
}
@endphp

@section('meta_title', 'Edit '.$name.' Permission')
@section('page_title', 'Edit '.$name.' Permission')
@section('page_title_icon')
<i class="metismenu-icon pe-7s-lock"></i>
@endsection

@section('content')
<section class="content pb-3">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form action="{{ route('admin.permission.update',$permission->id) }}?guard={{request('guard')}}" autocomplete="off" method="post" enctype="multipart/form-data" id="edit">
                    @csrf
                    @method('patch')
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                @if($errors->any())
                                @foreach($errors->all() as $error)
                                <div class="alert alert-danger" role="alert">
                                    <strong>{{$error}}</strong>
                                </div>
                                @endforeach
                                @endif
                            </div>

                            <input type="hidden" name="guard" value="{{request('guard')}}">
                            <input type="hidden" name="permission_group_id" value="{{request('permission_group_id')}}">

                            <div class="col-12">
                                <div class="form-group">
                                    <label>Permission Group</label>
                                    <input type="text" name="permission_group_name" class="form-control"
                                        value="{{request('permission_group_name')}}" readonly>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group">
                                    <label>Permission Name</label>
                                    <input type="text" name="name" value="{{ $permission->name }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center mt-3">
                            <a href="{{route('admin.permission-group.index')}}?guard={{request('guard')}}" class="btn btn-danger action-btn mr-2">Cancel</a>
                            <button type="submit" class="btn btn-primary action-btn">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
{!! JsValidator::formRequest('App\Http\Requests\StoreGroupPermission', '#edit'); !!}
<script>
@endsection
