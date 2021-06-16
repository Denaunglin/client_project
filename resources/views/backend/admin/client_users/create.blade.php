@extends('backend.admin.layouts.app')

@section('meta_title', 'Add User')
@section('page_title', 'Add User')
@section('page_title_icon')
<i class="metismenu-icon pe-7s-users"></i>
@endsection

@section('content')

<div class="row">
   
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.client-users.store') }}" method="post" id="form" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nrc_passport">NRC or Passport</label>
                                <input type="text" name="nrc_passport" id="nrc_passport" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth</label>
                                <input type="text" name="date_of_birth" id="date_of_birth" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gender</label>
                                <div class="row ">
                                    <div class="form-check ml-5">
                                        <input class="form-check-input" type="radio" name="gender" id="exampleRadios1" checked  value="male" >
                                        <label class="form-check-label" for="exampleRadios1">
                                          Male
                                        </label>
                                    </div>
                                    <div class="form-check ml-5">
                                        <input class="form-check-input" type="radio" name="gender" id="exampleRadios2" checked value="female" >
                                        <label class="form-check-label" for="exampleRadios2">
                                          Female
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address">Address </label>
                                <textarea  name="address" id="address" class="form-control"></textarea>
                            </div>
                        </div>

                       <div class="col-md-6">
                        <label for="">Profile Image</label>
                        <p><strong>Recommedation :</strong> Image size should be (1000 x 400 ) and under 2 MB </p>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="imageAddon"><i
                                        class="fas fa-cloud-upload-alt"></i></span>
                            </div>
                            <div class="custom-file">
                                <input type="file" name="image" class="custom-file-input" accept="image/*"
                                    id="image" aria-describedby="imageAddon" >
                                <label class="custom-file-label" for="image">Choose file</label>
                            </div>
                        </div>
                        <div class="image_preview"></div>
                       </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                        </div>

                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="roles">Roles</label>
                                <select class="form-control select2" name="roles[]" id="roles" multiple>
                                    @foreach($roles as $role)
                                        <option value="{{$role->name}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.client-users.index') }}" class="btn btn-danger mr-5">Cancel</a>
                            <input type="submit" value="Add" class="btn btn-success">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
$(function() {
  $('input[name="date_of_birth"]').daterangepicker({
    singleDatePicker: true,
    showDropdowns: true,
    minYear: 1901,
    locale: {
    format: 'YYYY-MM-DD'
    },
    maxYear: parseInt(moment().format('YYYY'),10)
  }, function(start, end, label) {
    var years = moment().diff(start, 'years');
  });
});
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

    //   $('#image').on('change', function() {
    //     let size = this.files[0].size; // this is in bytes
    //     if (size > 2000000) {
    //         swal("Image Size exceed than limit!", "Please rechoose under 2MB image!", "error");
    //     }
    // });

    </script>

{!! JsValidator::formRequest('App\Http\Requests\StoreClientUser', '#form') !!}
@endsection