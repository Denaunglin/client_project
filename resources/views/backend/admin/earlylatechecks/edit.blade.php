@extends('backend.admin.layouts.app')

@section('meta_title', 'Edit Early / Late-Check Prices')
@section('page_title', 'Edit Early / Late-Check Prices')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.earlylatechecks.update',[$earlylatecheck->id]) }}" method="post" id="form">
                    @csrf
                    @method('PUT')
                    <div class="row">
                         <div class="col-md-12">
                            <div class="form-group">    
                                <select name="user_id" id="" class="form-control select2">
                                <option value="">Select User Account Type</option> 
                                    @forelse($accounttype as $data)
                                <option value="{{$data->id}}" @if($data->id==$earlylatecheck->user_account_id) selected @endif>{{$data->name}}</option> 
                                @empty  
                                    @endforelse
                                </select>   
                                    
                            </label>    
                            </div>
                        </div>
                           <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Early Check-in Price MM</label>
                                <input value="{{$earlylatecheck->add_early_checkin_mm}}" type="number" step="any"  name="add_early_checkin_mm" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Early Check-in Price Foreign</label>
                                <input value="{{$earlylatecheck->add_early_checkin_foreign}}" type="number" step="any"  name="add_early_checkin_foreign" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Late Check-out Price MM</label>
                                <input value="{{$earlylatecheck->add_late_checkout_mm}}" type="number" step="any"  name="add_late_checkout_mm" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Add Late Check-out Price Foreign</label>
                                <input value="{{$earlylatecheck->add_late_checkout_foreign}}" type="number" step="any"  name="add_late_checkout_foreign" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Subtract Early Check-in Price MM</label>
                                <input value="{{$earlylatecheck->subtract_early_checkin_mm}}" type="number" step="any"  name="subtract_early_checkin_mm" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Subtract Early Check-in Price Foreign</label>
                                <input value="{{$earlylatecheck->subtract_early_checkin_foreign}}" type="number" step="any"  name="subtract_early_checkin_foreign" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Subtract Late Check-out Price MM</label>
                                <input value="{{$earlylatecheck->subtract_late_checkout_mm}}" type="number" step="any"  name="subtract_late_checkout_mm" class="form-control">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Subtract Late Check-out Price Foreign</label>
                                <input value="{{$earlylatecheck->subtract_late_checkout_foreign}}" type="number" step="any"  name="subtract_late_checkout_foreign" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.earlylatechecks.index') }}" class="btn btn-danger mr-3">Cancel</a>
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
{!! JsValidator::formRequest('App\Http\Requests\StoreBedType', '#form') !!}
@endsection
