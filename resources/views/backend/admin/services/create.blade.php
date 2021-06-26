@extends('backend.admin.layouts.app')
@section('meta_title', 'Add Service')
@section('page_title')
@lang("message.header.add_service")
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
                <form action="{{ route('admin.services.store') }}" method="post" id="create" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label> @lang("message.header.service_name") </label>
                                <input type="text" id="service_name" name="service_name" class="form-control  @error('service_name') is-invalid @enderror" >
                                @error('service_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label >@lang("message.description")</label>
                                <textarea name="description" class="form-control" id="description" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang("message.header.service_charges")</label>
                                <input type="number" id="service_charges" name="service_charges" class="form-control  @error('service_charges') is-invalid @enderror" >
                                @error('service_charges')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                        <div class="row my-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.services.index') }}" class="btn btn-danger mr-3">@lang("message.cancel")</a>
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

<script>
   
   
</script>
{!! JsValidator::formRequest('App\Http\Requests\ServiceRequest','#create') !!}
@endsection
