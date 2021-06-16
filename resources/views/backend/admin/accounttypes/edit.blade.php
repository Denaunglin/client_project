@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit Account Type')
@section('page_title', 'Edit Account Type')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.accounttypes.update',[$accounttype->id]) }}" method="post" id="form">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" value="{{$accounttype->name}}" id="name" name="name"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Commission Percentage </label>
                                <div class="input-group">
                                    <input type="number" value="{{$accounttype->commission}}" step="any" id="commission"
                                        name="commission" class="form-control">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Booking limit</label>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="custom-control custom-radio">
                                                <input type="radio" class="gender custom-control-input " @if($accounttype->booking_limit==1) checked @endif id="default1" value="1" name="booking_limit" >
                                                    <label class="custom-control-label" for="default1">On</label>
                                                </div>
                                            </div>

                                            <div class="col-md-12">
                                                <div class="custom-control custom-radio">
                                                <input type="radio" class="gender custom-control-input" @if($accounttype->booking_limit==0) checked @endif id="default2" value="0" name="booking_limit">
                                                    <label class="custom-control-label" for="default2">Off</label>
                                                </div>
                                            </div>
                                        </div> 
                            </div>
                         </div>


                    </div>


                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.accounttypes.index') }}" class="btn btn-danger mr-3">Cancel</a>
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
{!! JsValidator::formRequest('App\Http\Requests\AccounttypeRequest', '#form') !!}

<script>
    $('.pay-list').on('change', function() {
      $('.pay-list').not(this).prop('checked', false);
  });
</script>
@endsection
