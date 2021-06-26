@extends('backend.admin.layouts.app')
@section('meta_title', 'Edit Item')
@section('page_title')
@lang("message.header.edit_expense")
@endsection
@section('page_title_icon')

<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.expenses.update',$expense->id) }}" method="post" id="edit"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                      
                       
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.expense_category")</label>
                                <select class="form-control select2" id="expense_category_id" name="expense_category_id" required>
                                    <option value="">@lang("message.header.choose_expense_category")</option>
                                    @forelse($expense_category as $data)
                                    <option @if($expense->expense_category_id == $data->id) selected  @endif value="{{$data->id}}">{{$data->name }}</option>
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
                                    <option value="0">@lang("message.header.no_expense_type")</option>
                                    @forelse($expense_type as $data)
                                    <option @if($expense->expense_type_id == $data->id) selected  @endif  value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>@lang("message.header.there_is_no_data")</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.about") </label>
                                <textarea type="text" id="about" value="{{$expense->about}}" name="about" class="form-control  @error('about') is-invalid @enderror" >{{$expense->about}}</textarea>
                                @error('about')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                     
                        <div class="col-md-12">
                            <div class="form-group">
                                <label> @lang("message.price")</label>
                                <input type="number" id="price" value="{{$expense->price}}" name="price" class="form-control  @error('price') is-invalid @enderror" >
                                @error('price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>                       

                    <div class="row my-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.expenses.index') }}" class="btn btn-danger mr-3">@lang("message.cancel")</a>
                            <button type="submit"  class="btn btn-success">@lang("message.update")</button>
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

{!! JsValidator::formRequest('App\Http\Requests\ExpenseRequest', '#edit') !!}
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
