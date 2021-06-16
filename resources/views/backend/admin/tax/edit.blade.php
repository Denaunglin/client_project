@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit Room Type')
@section('page_title', 'Edit Room Type')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            
            <div class="card-body">
                <form action="{{route('admin.taxes.update',[$tax->id])}}" method="post" id="edit">
                    @csrf
                    @method("PUT")
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Tax</label>
                                <div class="input-group">
                                    <input type="number" value="{{$tax->amount}}" class="form-control" name="amount"
                                        required>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">%</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row my-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.taxes.index') }}" class="btn btn-danger mr-3">Cancel</a>
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
{!! JsValidator::formRequest('App\Http\Requests\TaxRequest', '#edit') !!}
@endsection
