@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit CheckIn Deposite')
@section('page_title', 'Edit CheckIn Deposite')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{route('admin.deposits.update',[$deposit->id])}}" method="post" id="edit">
                    @csrf
                    @method("PUT")
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Night</label>
                                <div class="input-group">
                                    <input type="number" value="{{$deposit->night}}" class="form-control" name="night"
                                        required>
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"></span>
                                    </div>

                                </div>
                            </div>
                            <div class="form-group">
                                <label>Deposit</label>
                                <div class="input-group">
                                    <input type="number" value="{{$deposit->deposit}}" class="form-control" name="deposit"
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
                            <a href="{{ route('admin.deposits.index') }}" class="btn btn-danger mr-3">Cancel</a>
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
{!! JsValidator::formRequest('App\Http\Requests\DepositRequest', '#edit') !!}
@endsection
