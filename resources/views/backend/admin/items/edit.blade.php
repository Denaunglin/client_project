@extends('backend.admin.layouts.app')
@section('meta_title', 'Edit Item')
@section('page_title', 'Edit Item')
@section('page_title_icon')

<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.items.update',$items->id) }}" method="post" id="edit"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                   <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Barcode </label>
                                <input type="number" value="{{$items->barcode}}" id="barcode" name="barcode" class="form-control  @error('barcode') is-invalid @enderror" >
                                @error('barcode')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item Name </label>
                                <input type="text" id="name" value="{{$items->name}}" name="name" class="form-control  @error('name') is-invalid @enderror" >
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item Category</label>
                                <select class="form-control select2" id="item_category_id" name="item_category_id" required>
                                    <option value="">Choose Item Category</option>
                                    @forelse($item_category as $data)
                                    <option @if($items->item_category_id == $data->id) selected  @endif value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>There is no data</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sub Item Category</label>
                                <select class="form-control select2" id="item_sub_category_id" name="item_sub_category_id" required>
                                    <option value="">Choose Sub Item Category</option>
                                    <option @if($items->item_sub_category_id == 0) selected @endif value="0">None</option>
                                    @forelse($item_sub_category as $data)
                                    <option @if($items->item_sub_category_id) selected @endif  value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>There is no data</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> Unit </label>
                               <input type="text" id="unit" value="{{$items->unit}}" name="unit" class="form-control  @error('unit') is-invalid @enderror" >
                                @error('unit')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Minimun Qty</label>
                            <input type="number" id="minimun_qty" value="{{$items->minimun_qty}}" name="minimun_qty" class="form-control  @error('minimun_qty') is-invalid @enderror" >
                                @error('minimun_qty')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Buying Price</label>
                            <input type="number" id="buying_price" value="{{$items->buying_price}}" name="buying_price" class="form-control  @error('buying_price') is-invalid @enderror" >
                                @error('buying_price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Retail Price</label>
                            <input type="number" id="retail_price" value="{{$items->retail_price}}" step="any" name="retail_price" class="form-control" required>
                            </div>
                        </div>
                          <div class="col-md-6">
                            <div class="form-group">
                                <label>Wholesale Price</label>
                            <input type="number" id="wholesale_price" value="{{$items->wholesale_price}}" step="any" name="wholesale_price" class="form-control" required>
                            </div>
                        </div>

                    <div class="col-md-12 mb-3">
                        <label for="">Item Image</label>
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
                            <img src="{{$items->image_path()}}" width="200px">
                        </div>                    
                    </div>
                   </div>
                    <div class="row my-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.items.index') }}" class="btn btn-danger mr-3">Cancel</a>
                            <button type="submit"  class="btn btn-success">Update</button>
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

        $('.custom-file-input').on('change', function() {
        let size = this.files[0].size; // this is in bytes
        if (size > 2000000) {
            swal("Image Size exceed than limit!", "Please rechoose back!", "error");
        }
    });

</script>

{!! JsValidator::formRequest('App\Http\Requests\ItemRequest', '#edit') !!}
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
