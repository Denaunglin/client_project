@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit Bussiness Info')
@section('page_title')
@lang("message.header.edit_bussiness_info")
@endsection
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.bussiness_infos.update',[$bussiness_infos->id]) }}" method="post" id="form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.header.bussiness_name") </label>
                                <input type="text" id="name" name="name"  value="{{$bussiness_infos->name}}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.header.bussiness_type") </label>
                                <input type="text" id="type" name="type"  value="{{$bussiness_infos->type}}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.email")</label>
                            <input type="email" id="email" value="{{$bussiness_infos->email}}" name="email" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.phone")</label>
                                <input type="text"  id="phone" value="{{$bussiness_infos->phone}}" name="phone" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.address")</label>
                                <textarea  id="address" name="address"  class="form-control">{{$bussiness_infos->address}}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="">@lang("message.header.bussiness_logo") </label>
                            <p><strong>Recommedation :</strong> Image size should be (1000 x 400 ) and under 2 MB </p>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="imageAddon"><i
                                            class="fas fa-cloud-upload-alt"></i></span>
                                </div>
                                <div class="custom-file">
                                    <input type="file" name="image" class="custom-file-input" accept="image/*"
                                        id="image" aria-describedby="imageAddon" required>
                                    <label class="custom-file-label" for="image">Choose file</label>
                                </div>
                            </div>
                            <div class="image_preview2">
                                <img src="{{$bussiness_infos->image_path()}}" width="200px">
                            </div>    
                        </div> 
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.bussiness_infos.index') }}" class="btn btn-danger mr-3">Cancel</a>
                            <input type="submit" value="@lang("message.update")" class="btn btn-success">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
{!! JsValidator::formRequest('App\Http\Requests\CardTypeRequest', '#form') !!}

<script>
     $(document).ready(function(){
        $('#image').on('change', function() {
            var total_file = document.getElementById("image").files.length;
            $('[for="image"]').html(total_file + ' files');
            $('.image_preview').html('');
            for (var i = 0; i < total_file; i++) {
                $('.image_preview').append("<img src='" + URL.createObjectURL(event.target.files[i]) + "' class='zoomify'>");
            }
        });
    });
</script>
@endsection
