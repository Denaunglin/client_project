@extends('backend.admin.layouts.app')

@section('meta_title', 'Add Other Service Item')
@section('page_title', 'Add Other Service Item')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.otherservicesitem.store') }}" method="post" id="create">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Other Service Category</label>
                                <select class="form-control select2" name="other_services_category_id" id="">
                                <option value="" >Select Category</option>
                                @forelse($other_services_category as $data)
                                <option value="{{$data->id}}">{{$data->name}}</option>
                                @empty 
                                <option >There is no category yet !</option>
                                @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" id="name" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Charges MM</label>
                                <input type="number" step="any" name="charges_mm" id="charges_mm" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Charges Foreign</label>
                                <input type="number" step="any" name="charges_foreign" id="charges_foreign" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.otherservicesitem.index') }}" class="btn btn-danger mr-3">Cancel</a>
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
{!! JsValidator::formRequest('App\Http\Requests\OtherServiceItem', '#create') !!}
@endsection
