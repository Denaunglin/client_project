@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit Extra Bed Prices')
@section('page_title', 'Edit Extra Bed Prices')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.extrabedprices.update',[$extrabedprice->id]) }}" method="post" id="form">
                    @csrf
                    @method('PUT')
                    <div class="row">
                         <div class="col-md-12">
                            <div class="form-group">    
                                <select name="user_id" id="" class="form-control select2">
                                <option value="">Select User Account Type</option> 
                                    @forelse($accounttype as $data)
                                <option value="{{$data->id}}" @if($data->id==$extrabedprice->id) selected @endif>{{$data->name}}</option> 
                                @empty  
    
                                    @endforelse
                                </select>   
                                    
                            </label>    
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Extra Bed Price MM</label>
                            <input type="number" step="any" value="{{$extrabedprice->add_extrabed_price_mm}}"  name="add_extrabed_price_mm" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Extra Bed Price Foreign</label>
                                <input type="number" step="any" value="{{$extrabedprice->add_extrabed_price_foreign}}"  name="add_extrabed_price_foreign" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Subtract Extra Bed Price MM</label>
                                <input type="number" step="any" value="{{$extrabedprice->subtract_extrabed_price_mm}}"  name="subtract_extrabed_price_mm" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Subtract Extra Bed Price Foreign</label>
                                <input type="number" step="any"  value="{{$extrabedprice->subtract_extrabed_price_foreign}}" name="subtract_extrabed_price_foreign" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.extrabedprices.index') }}" class="btn btn-danger mr-3">Cancel</a>
                            <input type="submit" value="Update" class="btn btn-success">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
{!! JsValidator::formRequest('App\Http\Requests\StoreBedType', '#form') !!}
@endsection
