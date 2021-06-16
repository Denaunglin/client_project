@extends('backend.admin.layouts.app')
@section('meta_title', 'Add OrderList')
@section('page_title', 'Add OrderList')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.order_lists.store') }}" method="post" id="create" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item Category</label>
                                <select class="form-control select2" id="item_category" name="item_category_id" required>
                                    <option value="">Choose Item Category</option>
                                    @forelse($item_category as $data)
                                    <option value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>There is no data</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item Sub Category</label>
                                <select class="form-control select2" id="item_sub_category" name="item_sub_category_id" required>
                                    <option value="">Choose Item Sub Category</option>
                                    <option value="0">None</option>
                                    @forelse($item_sub_category as $data)
                                    <option value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>There is no data</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item </label>
                                <select class="form-control select2" id="item_sub_category" name="item_sub_category_id" required>
                                    <option value="">Choose Item </option>
                                    <option value="0">None</option>
                                    @forelse($item as $data)
                                    <option value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>There is no data</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> Unit </label>
                                <input type="text" id="unit" name="unit" class="form-control  @error('unit') is-invalid @enderror" >
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
                                <label>Stock In Hand</label>
                                <input type="number" id="stock_in_hand" name="stock_in_hand" class="form-control  @error('stock_in_hand') is-invalid @enderror" >
                                @error('stock_in_hand')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                       
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>To Reorder </label>
                                <input type="number" id="to_reorder" step="any" name="to_reorder" class="form-control" required>
                            </div>
                        </div>

                    </div>
                        <div class="row my-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.items.index') }}" class="btn btn-danger mr-3">Cancel</a>
                            <input type="submit" value="Confirm" class="btn btn-success">
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
