@extends('backend.admin.layouts.app')

@section('meta_title', 'Add Customer')
@section('page_title')
@lang("message.header.add_customer")
@endsection
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
                                <label for="name">@lang("message.name")</label>
                                <input type="text" name="name" id="name" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">@lang("message.email")</label>
                                <input type="email" name="email" id="email" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">@lang("message.phone")</label>
                                <input type="text" name="phone" id="phone" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nrc_passport">@lang("message.nrc_or_passport")</label>
                                <input type="text" name="nrc_passport" id="nrc_passport" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_of_birth">@lang("message.date_of_birth")</label>
                                <input type="text" name="date_of_birth" id="date_of_birth" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.gender")</label>
                                <div class="row ">
                                    <div class="form-check ml-5">
                                        <input class="form-check-input" type="radio" name="gender" id="exampleRadios1" checked  value="male" >
                                        <label class="form-check-label" for="exampleRadios1">
                                            @lang("message.male")
                                        </label>
                                    </div>
                                    <div class="form-check ml-5">
                                        <input class="form-check-input" type="radio" name="gender" id="exampleRadios2" checked value="female" >
                                        <label class="form-check-label" for="exampleRadios2">
                                            @lang("message.female")
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address">@lang("message.address") </label>
                                <textarea  name="address" id="address" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.client-users.index') }}" class="btn btn-danger mr-5">@lang("message.cancel")</a>
                            <input type="submit" value="@lang("message.add")" class="btn btn-success">
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