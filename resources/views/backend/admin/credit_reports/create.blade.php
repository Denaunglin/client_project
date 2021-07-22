@extends('backend.admin.layouts.app')
@section('meta_title', 'Add Credit ')
@section('page_title')
@lang("message.header.add_credit")
@endsection
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('page_title_buttons')
<div class="d-flex justify-content-end">

    <a href="{{route('admin.client-users.create')}}" title="Add User" class="btn btn-primary action-btn">@lang("message.header.add_customer")</a>

</div>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.credit_reports.store') }}" method="post" id="create" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.customer") </label>
                                <select class="form-control select2" id="customer_id" name="customer_id" required>
                                    <option value="">@lang("message.header.choose_customer") </option>
                                    @forelse($customer as $data)
                                    <option value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>@lang("message.header.there_is_no_data")</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.item") </label>
                                <select class="form-control select2" id="item_id" name="item_id" required>
                                    <option value="">@lang("message.header.choose_item")</option>
                                    @forelse($item as $data)
                                    <option value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>@lang("message.header.there_is_no_data")</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="">@lang("message.header.qty")</label>
                                <input type="number" id="qty" name="qty" class="form-control @error('qty') is-invalid @enderror">
                                @error('qty')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.original_amount") </label>
                                <input type="number" id="origin_amount" name="origin_amount" class="form-control  @error('origin_amount') is-invalid @enderror" >
                                @error('origin_amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.paid_amount")</label>
                                <input type="number" id="paid_amount" name="paid_amount" class="form-control  @error('paid_amount') is-invalid @enderror" >
                                @error('paid_amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.credit_amount") </label>
                                <input type="number" id="credit_amount" name="credit_amount" class="form-control  @error('credit_amount') is-invalid @enderror" >
                                @error('credit_amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.paid_date") </label>
                                <input type="text" id="paid_date"  name="paid_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.paid_times")  </label>
                                <input type="number" id="paid_times" step="any" name="paid_times" class="form-control @error('paid_times') is-invalid @enderror">
                            </div>
                            @error('paid_times')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fully Paid</label>
                                <div class="row ">
                                    <div class="form-check ml-5">
                                        <input class="form-check-input" type="radio" name="paid_status" id="exampleRadios1" checked  value="0" >
                                        <label class="form-check-label" for="exampleRadios1">
                                            Paid
                                        </label>
                                    </div>
                                    <div class="form-check ml-5">
                                        <input class="form-check-input" type="radio" name="paid_status" id="exampleRadios2"  value="1" >
                                        <label class="form-check-label" for="exampleRadios2">
                                           Unpaid 
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="row my-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.credit_reports.index') }}" class="btn btn-danger mr-3">@lang("message.cancel") </a>
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
  $('input[name="paid_date"]').daterangepicker({
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
