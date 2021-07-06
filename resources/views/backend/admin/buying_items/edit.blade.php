@extends('backend.admin.layouts.app')
@section('meta_title', 'Edit Buying Item')
@section('page_title')
@lang("message.header.edit_buying_item")
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
                <form action="{{ route('admin.buying_items.update',$item->id) }}" method="post" id="edit"
                    enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                  
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.header.supplier")</label>
                                <select class="form-control custom-select" name="supplier_id"   required>
                                    <option value="">Choose Supplier </option>
                                    <option value="">@lang("message.header.no_supplier") </option>
                                    @forelse($suppliers as $data)
                                    <option @if($data->id == $item->supplier_id) selected @endif value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>@lang("message.header.there_is_no_data")</p>
                                    @endforelse
                                </select>
                            </div>
                            <div class="form-group">
                                <label>@lang("message.header.item")</label>
                                <select class="form-control select2" id="item_id" name="item_id" required>
                                    <option value="">@lang("message.header.choose_item_category")</option>
                                    @forelse($items as $data)
                                    <option @if($data->id == $item->item_id) selected @endif value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>@lang("message.header.there_is_no_data")</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> @lang("message.header.qty")</label>
                                <input type="number" value="{{$item->qty}}" id="qty" name="qty" class="form-control  @error('qty') is-invalid @enderror" >
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
                                <input type="number" value="{{$item->price}}" id="price" name="price" class="form-control  @error('price') is-invalid @enderror" >
                                @error('price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                      
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> @lang("message.header.total_price") </label>
                                <input type="number" id="net_price" value="{{$item->net_price}}"  name="net_price" class="form-control  @error('net_price') is-invalid @enderror" >
                                @error('net_price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.buying_items.index') }}" class="btn btn-danger mr-3"> @lang("message.cancel")</a>
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

        $('.custom-file-input').on('change', function() {
        let size = this.files[0].size; // this is in bytes
        if (size > 2000000) {
            swal("Image Size exceed than limit!", "Please rechoose back!", "error");
        }
    });

</script>

{!! JsValidator::formRequest('App\Http\Requests\BuyingItemRequest', '#edit') !!}

@endsection
