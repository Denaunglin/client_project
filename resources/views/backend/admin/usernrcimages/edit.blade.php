@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit User Nrc or Passport Image')
@section('page_title', 'Edit User Nrc or Passport Image')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.usernrcimages.update',[$usernrcimage->id]) }}"
                    enctype="multipart/form-data" method="post" id="form">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>User</label>
                                <select name="user_id" class="form-control select2">
                                    <option value="">Please select user</option>
                                    @forelse($user as $data)
                                    <option @if($data->id==$usernrcimage->user_id) selected @endif
                                        value="{{$data->id}}">{{$data->name}} / {{$data->phone}} / {{$data->address}}
                                    </option>
                                    @empty
                                    <option value=""></option>
                                    @endforelse
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="">Nrc or Passport Front Image</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="imageAddon"><i
                                            class="fas fa-cloud-upload-alt"></i></span>
                                </div>
                                <div class="custom-file">
                                    <input type="file" name="front_pic" class="custom-file-input1" accept="image/*"
                                        id="image1" aria-describedby="imageAddon">
                                    <label class="custom-file-label" for="image1">Choose file</label>
                                </div>
                            </div>
                            <div class="image_preview1">
                                <img src="{{$usernrcimage->image_path_front()}}" width="200px">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="">Nrc or Passport Back Image</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="imageAddon"><i
                                            class="fas fa-cloud-upload-alt"></i></span>
                                </div>
                                <div class="custom-file">
                                    <input type="file" name="back_pic" class="custom-file-input2" accept="image/*"
                                        id="image2" aria-describedby="imageAddon">
                                    <label class="custom-file-label" for="image2">Choose file</label>
                                </div>
                            </div>
                            <div class="image_preview2">
                                <img src="{{$usernrcimage->image_path_back()}}" width="200px">
                            </div>
                        </div>
                    </div>

                    <div class="row my-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.usernrcimages.index') }}" class="btn btn-danger mr-3">Cancel</a>
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

//     $('.custom-file-input1').on('change', function() {
//     let size = this.files[0].size; // this is in bytes
//     if (size > 2000000) {
//         swal("Image Size exceed than limit!", "Please rechoose back!", "error");
//         document.querySelector('.custom-file-input1').value = '';

//     }
// });
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

//     $('.custom-file-input2').on('change', function() {
//     let size = this.files[0].size; // this is in bytes
//     if (size > 2000000) {
//         swal("Image Size exceed than limit!", "Please rechoose back!", "error");
//         document.querySelector('.custom-file-input2').value = '';

//     }
// });

</script>
@endsection
