@extends('backend.admin.layouts.app')
@section('meta_title', 'Edit Return Item')
@section('page_title')
@lang("message.header.edit_return_item")
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
                <form action="{{ route('admin.return_items.update',$data_item->id) }}" method="post" id="edit"
                    enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                  
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label><h6>@lang("message.header.return_type")</h6></label>
                                <select name="return_type" id="" class="form-control">
                                    <option @if($data_item->return_type == 0) selected @endif value="0">Buying Return</option>
                                    <option @if($data_item->return_type == 1) selected @endif value="1">Sale Return</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.item") </label>
                                <select class="form-control select2" id="item_id" name="item_id" required>
                                    <option value="">Choose Item Category</option>
                                    @forelse($items as $data)
                                    <option @if($data_item->item_id == $data->id) selected @endif value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>There is no data</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                      
                       
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> @lang("message.header.qty")</label>
                                <input type="number" id="qty" value="{{$data_item->qty}}" name="qty" class="form-control  @error('qty') is-invalid @enderror" >
                                @error('qty')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> @lang("message.header.rate_per_unit")</label>
                                <input type="number" id="price" value="{{$data_item->price}}" name="price" class="form-control  @error('price') is-invalid @enderror" >
                                @error('price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> @lang("message.header.discount")</label>
                                <input type="number" id="discount" value="{{$data_item->discount}}" name="discount" class="form-control  @error('discount') is-invalid @enderror" >
                                @error('discount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label> @lang("message.header.total_price")</label>
                                <input type="number" id="net_price" value="{{$data_item->net_price}}" name="net_price" class="form-control  @error('net_price') is-invalid @enderror" >
                                @error('net_price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.return_items.index') }}" class="btn btn-danger mr-3">@lang("message.cancel")</a>
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
{!! JsValidator::formRequest('App\Http\Requests\BuyingItemRequest', '#edit') !!}

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
