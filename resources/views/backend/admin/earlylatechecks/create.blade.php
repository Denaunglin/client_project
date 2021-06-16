@extends('backend.admin.layouts.app')

@section('meta_title', ' Early / Late-Check Prices')
@section('page_title', ' Early / Late-Check Prices')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.earlylatechecks.store') }}" method="post" id="create">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">    
                                <select name="user_id" id="" class="form-control select2">
                                <option value="">Select User Account Type</option> 
                                    @forelse($accounttype as $data)
                                <option value="{{$data->id}}">{{$data->name}}</option> 
                                @empty  
                                
                                    @endforelse
                                </select>   
                                    
                            </label>    
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Early Check-in Price MM</label>
                                <input type="number" step="any"  name="add_early_checkin_mm" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Early Check-in Price Foreign</label>
                                <input type="number" step="any"  name="add_early_checkin_foreign" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Late Check-out Price MM</label>
                                <input type="number" step="any"  name="add_late_checkout_mm" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Late Check-out Price Foreign</label>
                                <input type="number" step="any"  name="add_late_checkout_foreign" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Subtract Early Check-in Price MM</label>
                                <input type="number" step="any"  name="subtract_early_checkin_mm" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Subtract Early Check-in Price Foreign</label>
                                <input type="number" step="any"  name="subtract_early_checkin_foreign" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Subtract Late Check-out Price MM</label>
                                <input type="number" step="any"  name="subtract_late_checkout_mm" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Subtract Late Check-out Price Foreign</label>
                                <input type="number" step="any"  name="subtract_late_checkout_foreign" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.earlylatechecks.index') }}" class="btn btn-danger mr-3">Cancel</a>
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
{!! JsValidator::formRequest('App\Http\Requests\StoreBedType', '#create') !!}
@endsection
