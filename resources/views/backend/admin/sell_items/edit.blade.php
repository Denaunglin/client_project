@extends('backend.admin.layouts.app')
@section('meta_title', 'Edit Commodity Sales Item')
@section('page_title')
@lang("message.header.edit_selling_item")
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
                <form action="{{ route('admin.sell_items.update',$data_item->id) }}" method="post" id="edit"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                  
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.item") </label>
                                <select class="form-control select2" id="item_id" name="item_id" multiple required>
                                    <option value="">Choose Item </option>
                                    @forelse($items as $data)
                                    @foreach($item_id as $id_item)
                                        <option @if($data->id == $id_item) selected @endif value="{{$data->id}}">{{$data->name }}</option>
                                    @endforeach
                                    @empty<p>There is no data</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>@lang("message.header.customer") </label>
                                <select class="form-control custom_select" id="item_id" name="item_id"  required>
                                    <option value="">Choose Customer </option>
                                    @forelse($customer as $data)
                                <option @if($data->id == $data_item->customer) selected @endif value="{{$data->id}}">{{$data->name }} ( {{$data->accounttype->name}} )</option>
                                    @empty<p>There is no data</p>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label> @lang("message.header.discount")</label>
                            <input type="number" value="{{$data_item->discount}}" id="discount" name="discount" class="form-control  @error('discount') is-invalid @enderror" >
                                @error('discount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Total Qty</label>
                                <input type="number" id="total_qty" value="{{$data_item->total_qty}}" name="total_qty" class="form-control  @error('total_qty') is-invalid @enderror" >
                                @error('total_qty')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Total Amount</label>
                                <input type="number" id="total_amount" value="{{$data_item->total_amount}}" name="total_amount" class="form-control  @error('total_amount') is-invalid @enderror" >
                                @error('total_amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Paid Amount</label>
                                <input type="number" id="paid_amount" value="{{$data_item->paid_amount}}" name="paid_amount" class="form-control  @error('paid_amount') is-invalid @enderror" >
                                @error('paid_amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Credit Amount</label>
                                <input type="number" id="credit_amount" value="{{$data_item->credit_amount}}" name="credit_amount" class="form-control  @error('credit_amount') is-invalid @enderror" >
                                @error('credit_amount')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label> @lang("message.header.total_price")</label>
                                <input type="number" id="net_price" value="{{$data_item->total_amount}}" name="total_amount" class="form-control  @error('net_price') is-invalid @enderror" >
                                @error('net_price')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fully Paid</label>
                                <div class="row ">
                                    <div class="form-check ml-5">
                                        <input  class="form-check-input" type="radio" name="paid_status" id="exampleRadios1" @if($data_item->paid_status == 0) checked @endif  value="0" >
                                        <label class="form-check-label" for="exampleRadios1">
                                            Paid
                                        </label>
                                    </div>
                                    <div class="form-check ml-5">
                                        <input class="form-check-input" type="radio" name="paid_status" id="exampleRadios2" @if($data_item->paid_status == 1) checked @endif    value="1" >
                                        <label class="form-check-label" for="exampleRadios2">
                                           Unpaid 
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.buying_items.index') }}" class="btn btn-danger mr-3">@lang("message.cancel")</a>
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

@endsection
