@extends('backend.admin.layouts.app')

@section('meta_title', 'Add Slider')
@section('page_title', 'Add Slider')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.slider_upload.store') }}" method="post" enctype="multipart/form-data" id="create">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Slider Image</label>
                                <input type="file" class="form-control " name="slider_image"  id="image1" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.slider_upload.index') }}" class="btn btn-danger mr-3">Cancel</a>
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

{!! JsValidator::formRequest('App\Http\Requests\UserNrcPicRequest', '#create') !!}
@endsection
