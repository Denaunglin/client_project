@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit Shop Storage')
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
                <form action="{{ route('admin.shop_storages.update',[$shop_storage->id]) }}" method="post" id="form">
                    @csrf
                    @method('POST')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="">@lang("message.header.item") </label>
                                <select class="form-control custom-select" id="item_id" name="item_id" required>
                                    <option value="">@lang("message.header.choose_item")</option>
                                    @forelse($item as $data)
                                    <option @if($data->id == $shop_storage->item_id) selected @endif value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>@lang("message.header.there_is_no_data")</p>
                                    @endforelse
                                </select>      
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.header.qty")</label>
                            <input type="number" value="{{$shop_storage->qty}}" id="qty" name="qty" class="form-control">
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
{!! JsValidator::formRequest('App\Http\Requests\ItemCategoryRequest', '#form') !!}
@endsection
