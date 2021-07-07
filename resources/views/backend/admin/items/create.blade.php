@extends('backend.admin.layouts.app')
@section('meta_title', 'Add Item')
@section('page_title')
@lang("message.header.add_item")
@endsection
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('page_title_buttons')
<div class="d-flex justify-content-end">
    <a href="{{route('admin.item_categories.create')}}" title="Add Category" class="btn btn-primary action-btn">@lang("message.header.add_item_category")</a> &emsp13;
    <a href="{{route('admin.item_sub_categories.create')}}" title="Add Category" class="btn btn-primary action-btn">@lang("message.header.add_item_sub_category")</a>
</div>
@endsection
@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.items.store') }}" method="post" id="create" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.item_name") </label>
                                <input type="text" id="name" name="name" class="form-control  @error('name') is-invalid @enderror" >
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.item_category")</label>
                                <select class="form-control select2" id="item_category" name="item_category_id" required>
                                    <option value="">@lang("message.header.choose_item_category")</option>
                                    @forelse($item_category as $data)
                                    <option value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>@lang("message.header.there_is_no_data")</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.item_sub_category")</label>
                                <select class="form-control select2" id="item_sub_category" name="item_sub_category_id" required>
                                    <option value="">@lang("message.header.choose_item_sub_category")</option>
                                    @forelse($item_sub_category as $data)
                                    <option value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>@lang("message.header.there_is_no_data")</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                      
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.minimun_qty")</label>
                                <input type="number" id="minimun_qty" name="minimun_qty" class="form-control  @error('minimun_qty') is-invalid @enderror" >
                                @error('minimun_qty')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.buying_price")</label>
                                <input type="number" id="buying_price" name="buying_price" class="form-control  @error('buying_price') is-invalid @enderror" >
                                @error('buying_price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.retail_price")</label>
                                <input type="number" id="retail_price" step="any" name="retail_price" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.wholesale_price")</label>
                                <input type="number" id="wholesale_price" step="any" name="wholesale_price" class="form-control" required>
                            </div>
                        </div>
                    
                    </div>
                    <div class="row my-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.items.index') }}" class="btn btn-danger mr-3">@lang("message.cancel")</a>
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
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

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

    //   $('#image').on('change', function() {
    //     let size = this.files[0].size; // this is in bytes
    //     if (size > 2000000) {
    //         swal("Image Size exceed than limit!", "Please rechoose under 2MB image!", "error");
    //     }
    // });
   
</script>
{!! JsValidator::formRequest('App\Http\Requests\ItemRequest','#create') !!}
<script>
        $(function() {
  $('input[name="expire_date"]').daterangepicker({
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
</script>
@endsection
