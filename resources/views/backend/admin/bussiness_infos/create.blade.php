@extends('backend.admin.layouts.app')

@section('meta_title', 'Add Bussiness Info')
@section('page_title')
@lang("message.header.add_bussiness_info")
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
                <form action="{{ route('admin.bussiness_infos.store') }}" method="post" id="create" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label> @lang("message.header.bussiness_name")</label>
                                <input type="text" id="name" name="name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label> @lang("message.header.bussiness_type") </label>
                                <input type="text" id="type" name="type"class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.email")</label>
                                <input type="email" id="email" name="email" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.phone")</label>
                                <input type="text"  id="phone" name="phone"  class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.address")</label>
                                <textarea  id="address" name="address"  class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="">@lang("message.header.bussiness_logo")</label>
                            <p><strong>Recommedation :</strong> Image size should be (1000 x 400 ) and under 2 MB </p>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="imageAddon"><i
                                            class="fas fa-cloud-upload-alt"></i></span>
                                </div>
                                <div class="custom-file">
                                    <input type="file" name="image" class="custom-file-input" accept="image/*"
                                        id="image" aria-describedby="imageAddon" required>
                                    <label class="custom-file-label" for="image">@lang("message.header.choose_file")</label>
                                </div>
                            </div>
                            <div class="image_preview"></div>
                        </div> 
                        
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.bussiness_infos.index') }}" class="btn btn-danger mr-3">@lang("message.cancel")</a>
                            <input type="submit" value="@lang("message.confirm")" class="btn btn-success">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
{!! JsValidator::formRequest('App\Http\Requests\BussinessInfoRequest', '#create') !!}
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
