@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit Slider')
@section('page_title', 'Edit Slider')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.slider_upload.update',[$sliders->id]) }}"
                    enctype="multipart/form-data" method="post" id="form">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <label for="">Slider Image</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="imageAddon"><i class="fas fa-cloud-upload-alt"></i></span>
                                </div>
                                <div class="custom-file">
                                    <input type="file" name="slider_image" class="custom-file-input1" accept="image/*"
                                        id="image1" aria-describedby="imageAddon">
                                    <label class="custom-file-label" for="image1">Choose file</label>
                                </div>
                            </div>
                            <div class="image_preview1">
                                <img src="{{$sliders->image_path()}}" width="200px">
                            </div>
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.slider_upload.index') }}" class="btn btn-danger mr-3">Cancel</a>
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
<script>
    $(document).ready(function(){
        $('#image1').on('change', function() {
            var total_file = document.getElementById("image1").files.length;
            $('[for="image1"]').html(total_file + ' files');
            $('.image_preview1').html('');
            for (var i = 0; i < total_file; i++) {
                $('.image_preview1').append("<img src='" + URL.createObjectURL(event.target.files[i]) + "' class='zoomify' width='100px'>");
            }
        });
    });

 $(document).ready(function(){
        $('#image2').on('change', function() {
            var total_file = document.getElementById("image2").files.length;
            $('[for="image2"]').html(total_file + ' files');
            $('.image_preview2').html('');
            for (var i = 0; i < total_file; i++) {
                $('.image_preview2').append("<img src='" + URL.createObjectURL(event.target.files[i]) + "' class='zoomify' width='100px'>");
            }
        });
    });

</script>
@endsection
