@extends('backend.admin.layouts.app')
@section('meta_title', 'Add Expense')
@section('page_title')
@lang("message.header.add_expense")
@endsection
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('page_title_buttons')
<div class="d-flex justify-content-end">
    <a href="{{route('admin.expense_categories.create')}}" title="Add Expense Category" class="btn btn-primary action-btn">@lang("message.header.add_expense_category")</a> &emsp13;
    <a href="{{route('admin.expense_types.create')}}" title="Add Category" class="btn btn-primary action-btn">@lang("message.header.add_expense_type")</a>
</div>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.expenses.store') }}" method="post" id="create" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                       
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.expense_category")</label>
                                <select class="form-control select2" id="expense_category_id" name="expense_category_id" required>
                                    <option value="">@lang("message.header.choose_expense_category")</option>
                                    @forelse($expense_categories as $data)
                                    <option value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>@lang("message.header.there_is_no_data")</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.expense_type")</label>
                                <select class="form-control select2" id="expense_type_id" name="expense_type_id" required>
                                    <option value="">@lang("message.header.choose_expense_type")</option>
                                    @forelse($expense_types as $data)
                                    <option value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>@lang("message.header.add_expense")</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                     
                        <div class="col-md-12">
                            <div class="form-group">
                                <label> @lang("message.header.price")</label>
                                <input type="number" id="price" name="price" class="form-control  @error('price') is-invalid @enderror" >
                                @error('price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.about") </label>
                               <textarea type="text" id="about" name="about" class="form-control  @error('about') is-invalid @enderror" ></textarea>
                                @error('about')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.expenses.index') }}" class="btn btn-danger mr-3">@lang("message.cancel") </a>
                            <input type="submit" value="@lang("message.confirm") " class="btn btn-success">
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
{!! JsValidator::formRequest('App\Http\Requests\ExpenseRequest','#create') !!}
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
