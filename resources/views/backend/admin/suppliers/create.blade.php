@extends('backend.admin.layouts.app')

@section('meta_title', 'Add Supplier')
@section('page_title', 'Add Supplier')
@section('page_title_icon')
<i class="metismenu-icon pe-7s-users"></i>
@endsection

@section('content')

<div class="row">
   
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.suppliers.store') }}" method="post" id="form" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" id="name" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="number" name="phone" id="phone" class="form-control">
                            </div>
                        </div>

                       
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address">Address </label>
                                <textarea  name="address" id="address" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.suppliers.index') }}" class="btn btn-danger mr-5">Cancel</a>
                            <input type="submit" value="Add" class="btn btn-success">
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
{{-- {!! JsValidator::formRequest('App\Http\Requests\StoreClientUser', '#form') !!} --}}
@endsection