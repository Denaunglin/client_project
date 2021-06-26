@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit Item Sub Category')
@section('page_title')
@lang("message.header.edit_item_sub_category")
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
                <form action="{{ route('admin.item_sub_categories.update', [$item_sub_category->id]) }}" method="post" id="edit">
                    @csrf
                    @method("PUT")
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.name")</label>
                                <input type="text" id="name" value="{{$item_sub_category->name}}" name="name"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                       
                    <div class="row my-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.item_sub_categories.index') }}" class="btn btn-danger mr-3">@lang("message.cancel")</a>
                            <input type="submit" value="@lang("message.update")" class="btn btn-success">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
{!! JsValidator::formRequest('App\Http\Requests\ItemSubCategoryRequest', '#edit') !!}
@endsection
