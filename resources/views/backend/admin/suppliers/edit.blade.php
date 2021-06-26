@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit Supplier')
@section('page_title')
@lang("message.header.edit_supplier")
@endsection
@section('page_title_icon')
<i class="metismenu-icon pe-7s-users"></i>
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.suppliers.update',['supplier' => $suppliers->id]) }}" method="POST" id="edit" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">@lang('message.name')</label>
                                <input type="text" name="name" value="{{$suppliers->name}}" id="name" class="form-control">
                                </div>
                            </div>
    
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">@lang('message.phone')</label>
                                <input type="text" name="phone" value="{{$suppliers->phone}}" id="phone" class="form-control">
                                </div>
                            </div>
    
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">@lang('message.address') </label>
                                <textarea  name="address" id="address" class="form-control">{{$suppliers->address}}</textarea>
                                </div>
                            </div>
                        </div>
    
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <a href="{{ route('admin.client-users.index') }}" class="btn btn-danger mr-5">@lang('message.cancel')</a>
                                <input type="submit" value="@lang('message.confirm')" class="btn btn-success">
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

{!! JsValidator::formRequest('App\Http\Requests\SupplierRequest', '#edit') !!}
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

    //      $('#image').on('change', function() {
    //     let size = this.files[0].size; // this is in bytes
    //     if (size > 2000000) {
    //         swal("Image Size exceed than limit!", "Please rechoose under 2MB image!", "error");
    //     }
    // });

</script>
@endsection