@extends('backend.admin.layouts.app')

@section('meta_title', 'Add Account Type')
@section('page_title')
@lang("message.header.add_account_type")
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
                <form action="{{ route('admin.accounttypes.store') }}" method="post" id="create">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.name")</label>
                                <input type="text" id="name" name="name" class="form-control">
                            </div>
                        </div>
                     
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.accounttypes.index') }}" class="btn btn-danger mr-3">@lang("message.cancel")</a>
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
{!! JsValidator::formRequest('App\Http\Requests\AccounttypeRequest', '#create') !!}
<script>
    $('.pay-list').on('change', function() {
        $('.pay-list').not(this).prop('checked', false);
    });
</script>
@endsection
