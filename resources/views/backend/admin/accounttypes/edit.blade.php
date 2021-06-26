@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit Account Type')
@section('page_title')
@lang("message.header.edit_account_type")
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
                <form action="{{ route('admin.accounttypes.update',[$accounttype->id]) }}" method="post" id="form">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.name")</label>
                                <input type="text" value="{{$accounttype->name}}" id="name" name="name"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.header.commission_percentage") </label>
                                <div class="input-group">
                                    <input type="number" value="{{$accounttype->commission}}" step="any" id="commission"
                                        name="commission" class="form-control">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.accounttypes.index') }}" class="btn btn-danger mr-3">@lang("message.cancel")</a>
                            <input type="submit" value="@lang('message.confirm')" class="btn btn-success">
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
