@extends('backend.admin.layouts.app')

@section('meta_title', 'Add Shop Storage')
@section('page_title', 'Add Shop Storage')
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
                        <div class="col-md-12">
                            <div class="form-group">
                                <select class="form-control custom-select" id="item_id" name="item_id" required>
                                    <option value="">Choose Item Category</option>
                                    @forelse($item as $data)
                                    <option value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>There is no data</p>
                                    @endforelse
                                </select>      
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Qty</label>
                                <input type="number" id="qty" name="qty" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.shop_storages.index') }}" class="btn btn-danger mr-3">Cancel</a>
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
{!! JsValidator::formRequest('App\Http\Requests\ItemCategoryRequest', '#create') !!}
@endsection
