@extends('backend.admin.layouts.app')
@section('meta_title', 'Edit Item Ledger')
@section('page_title')
@lang("message.header.edit_item_ledger")
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
                <form action="{{ route('admin.item_ledgers.update',$item_ledger->id) }}" method="post" id="edit"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item </label>
                                <select class="form-control select2" id="item_sub_category" name="item_sub_category_id" required>
                                    <option value="">Choose Item </option>
                                    <option value="0">None</option>
                                    @forelse($item as $data)
                                    <option @if($item_ledger->item_id == $data->id) @endif value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>There is no data</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> Opening Qty </label>
                                <input type="text" id="opening_qty" value="{{$item_ledger->opening_qty}}" name="opening_qty" class="form-control  @error('opening_qty') is-invalid @enderror" >
                                @error('opening_qty')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> Buying Buy </label>
                                <input type="text" id="buying_buy" name="buying_buy" value="{{$item_ledger->buying_buy}}" class="form-control  @error('buying_buy') is-invalid @enderror" >
                                @error('buying_buy')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div><div class="col-md-6">
                            <div class="form-group">
                                <label> Buying Back </label>
                                <input type="text" id="buying_back" name="buying_back" value="{{$item_ledger->buying_back}}" class="form-control  @error('buying_back') is-invalid @enderror" >
                                @error('buying_back')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div><div class="col-md-6">
                            <div class="form-group">
                                <label> Selling Sell </label>
                                <input type="text" id="selling_sell" name="selling_sell" value="{{$item_ledger->selling_sell}}" class="form-control  @error('selling_sell') is-invalid @enderror" >
                                @error('selling_sell')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div><div class="col-md-6">
                            <div class="form-group">
                                <label> Selling Back </label>
                                <input type="text" id="selling_back" name="selling_back" class="form-control  @error('selling_back') is-invalid @enderror" >
                                @error('selling_back')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Adjust In</label>
                                <input type="number" id="adjust_in" name="adjust_in" class="form-control  @error('adjust_in') is-invalid @enderror" >
                                @error('adjust_in')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Adjust Out</label>
                                <input type="number" id="adjust_out" name="adjust_out" class="form-control  @error('adjust_out') is-invalid @enderror" >
                                @error('adjust_out')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>             <div class="col-md-6">
                            <div class="form-group">
                                <label>Adjust List</label>
                                <input type="number" id="adjust_list" name="adjust_list" class="form-control  @error('adjust_list') is-invalid @enderror" >
                                @error('adjust_list')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>             
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Closing Qty</label>
                                <input type="number" id="closing_qty" name="closing_qty" class="form-control  @error('closing_qty') is-invalid @enderror" >
                                @error('closing_qty')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.item_ledgers.index') }}" class="btn btn-danger mr-3">@lang("message.cancel")</a>
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
