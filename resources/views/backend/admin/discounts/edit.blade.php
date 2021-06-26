@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit Discount ')
@section('page_title')
@lang("message.header.edit_discount")
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
                <form action="{{ route('admin.discounts.update',[$discount->id]) }}" method="post" id="form">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.header.customer_type")</label>
                                <select name="user_account_id" class="form-control select2">
                                    <option  value="">@lang("message.header.select_customer_type")</option>
                                    @forelse($user_account_type as $data)
                                <option value="{{$data->id}}" @if($discount->user_account_id==$data->id) selected  @endif >{{$data->name}}</option>
                                    @empty
                                    <option >@lang("message.header.there_is_no_data")</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.header.item")</label>
                                <select name="item_id" class="form-control select2">
                                    <option value="">@lang("message.header.select_item")</option>
                                    @forelse($items as $data)
                                <option @if($discount->item_id == $data->id) selected @endif value="{{$data->id}}">{{$data->name}} </option>
                                    @empty
                                    <option >@lang("message.header.there_is_no_data")</option>
                                    @endforelse
                                </select>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang("message.header.discount_percentage")</label>
                                        <div class="input-group">
                                        <input type="number" value="{{$discount->discount_percentage_mm}}"  step="any" id="discount_percentage_mm" name="discount_percentage_mm" class="form-control">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang("message.header.discount_amount")</label>
                                    <input type="number" value="{{$discount->discount_amount_mm}}"  step="any" id="discount_amount_mm" name="discount_amount_mm" class="form-control">
                                    </div>
                                </div>
                            </div>   
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.discounts.index') }}" class="btn btn-danger mr-3">Cancel</a>
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
{!! JsValidator::formRequest('App\Http\Requests\DiscountsRequest', '#form') !!}
<script>
    $('.pay-list').on('change', function() {
      $('.pay-list').not(this).prop('checked', false);  
  });
</script>
@endsection
