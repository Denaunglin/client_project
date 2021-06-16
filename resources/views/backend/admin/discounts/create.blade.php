@extends('backend.admin.layouts.app')

@section('meta_title', 'Add Discount & Addon ')
@section('page_title', 'Add Discount & Addon ')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.discounts.store') }}" method="post" id="create">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>User Type</label>
                                <select name="user_account_id" class="form-control select2">
                                    <option  value="">Select User Account Type</option>
                                    @forelse($user_account_type as $data)
                                <option value="{{$data->id}}">{{$data->name}}</option>
                                    @empty
                                    <option >There is no data !</option>
                                    @endforelse
                                </select>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Room Type</label>
                                <select name="room_type_id" class="form-control select2">
                                    <option value="">Select Room Type</option>
                                    @forelse($room_type as $data)
                                <option value="{{$data->id}}">{{$data->roomtype->name}} / {{$data->bedtype->name}}</option>
                                    @empty
                                    <option >There is no data !</option>
                                    @endforelse
                                </select>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Discount Percentage MM</label>
                                        <div class="input-group">
                                            <input type="number"  step="any" id="discount_percentage_mm" name="discount_percentage_mm" class="form-control">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">%</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Discount Percentage Foreign</label>
                                        <div class="input-group">
                                            <input type="number"  step="any" id="discount_percentage_foreign" name="discount_percentage_foreign" class="form-control">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>   
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label>Discount Amount MM</label>
                                      <input type="number"  step="any" id="discount_amount_mm" name="discount_amount_mm" class="form-control">
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label>Discount Amount Foreign</label>
                                      <input type="number"  step="any" id="discount_amount_foreign" name="discount_amount_foreign" class="form-control">
                                  </div>
                              </div>
                          </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Addon Percentage MM</label>
                                        <div class="input-group">
                                            <input type="number"  step="any" id="addon_percentage_mm" name="addon_percentage_mm" class="form-control">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">%</span>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Addon Percentage Foreign</label>
                                        <div class="input-group">
                                            <input type="number"  step="any" id="addon_percentage_foreign" name="addon_percentage_foreign" class="form-control">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>   
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label>Addon Amount MM</label>
                                      <input type="number"  step="any" id="addon_amount_mm" name="addon_amount_mm" class="form-control">
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label>Addon Amount Foreign</label>
                                      <input type="number"  step="any" id="addon_amount_foreign" name="addon_amount_foreign" class="form-control">
                                  </div>
                              </div>
                          </div>
                        </div>
                        
                    </div>
                       
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.discounts.index') }}" class="btn btn-danger mr-3">Cancel</a>
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
{!! JsValidator::formRequest('App\Http\Requests\DiscountsRequest', '#create') !!}
<script>
      $('.pay-list').on('change', function() {
        $('.pay-list').not(this).prop('checked', false);  
    });
</script>
@endsection
