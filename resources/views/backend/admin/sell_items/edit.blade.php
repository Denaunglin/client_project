@extends('backend.admin.layouts.app')
@section('meta_title', 'Edit Commodity Sales Item')
@section('page_title', 'Edit Commodity Sales Item')
@section('page_title_icon')

<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.sell_items.update',$data_item->id) }}" method="post" id="edit"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                  
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item </label>
                                <select class="form-control select2" id="item_id" name="item_id" required>
                                    <option value="">Choose Item Category</option>
                                    @forelse($items as $data)
                                    <option @if($data->id == $data_item->item_id) selected @endif value="{{$data->id}}">{{$data->name }}</option>
                                    @empty<p>There is no data</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> Qty</label>
                            <input type="number" value="{{$data_item->qty}}" id="qty" name="qty" class="form-control  @error('qty') is-invalid @enderror" >
                                @error('qty')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> Price</label>
                                <input type="number" id="price" value="{{$data_item->price}}" name="price" class="form-control  @error('price') is-invalid @enderror" >
                                @error('price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> Discount</label>
                                <input type="number" id="discount" value="{{$data_item->discount}}" name="discount" class="form-control  @error('discount') is-invalid @enderror" >
                                @error('discount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label> Total Price</label>
                                <input type="number" id="net_price" value="{{$data_item->net_price}}" name="net_price" class="form-control  @error('net_price') is-invalid @enderror" >
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
                            <a href="{{ route('admin.buying_items.index') }}" class="btn btn-danger mr-3">Cancel</a>
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

@endsection
