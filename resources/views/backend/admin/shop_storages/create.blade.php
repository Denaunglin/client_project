@extends('backend.admin.layouts.app')

@section('meta_title', 'Add Shop Storage')
@section('page_title')
@lang("message.header.add_shop_storage")
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
                <form action="{{ route('admin.shop_storages.store') }}" method="post" id="create">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <span class="fa fa-search mt-3" id="show_search"></span>
                        </div>
                        <div class="col-md-12 mb-3"  id="hide_search">
                            <input type="search" id="search" autocomplete="off" name="search"  placeholder="search" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">@lang("message.header.item") </label>
                                <select class="form-control custom-select" id="item_id" name="item_id" required>
                                    <option value="">@lang("message.header.choose_item")</option>
                                    @forelse($item as $data)
                                    <option value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>@lang("message.header.there_is_no_data")</p>
                                    @endforelse
                                </select>      
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.header.qty")</label>
                                <input type="number" id="qty" name="qty" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.shop_storages.index') }}" class="btn btn-danger mr-3">@lang("message.cancel")</a>
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
<script>
      $(document).ready(function(){
      $('#hide_search').hide();
    });

    $('#show_search').on('click',function(e){
         $('#hide_search').show();    
    })


    $('#search').change(function(e) {
            let search = $(this).val();
            $.get('/get_item?search=' + search, function(data) {
                    $('#item_id').empty();
        $('#item_id').append('<option disabled selected>'+ 'Choose Item' + '</option>');
                    $.each(data, function( key, value ) {
                      $('#item_id').append('<option value="'+value.id+'" >'+value.name+'</option>');
        });
    });
});


</script>
{!! JsValidator::formRequest('App\Http\Requests\ItemCategoryRequest', '#create') !!}
@endsection
