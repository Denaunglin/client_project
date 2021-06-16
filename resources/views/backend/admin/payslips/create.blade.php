@extends('backend.admin.layouts.app')

@section('meta_title', 'Add Payslip')
@section('page_title', 'Add Payslip')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.payslips.store') }}" method="post" enctype="multipart/form-data" id="create">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Booking</label>
                                <select name="booking_no" class="form-control select2">
                                    <option value="">Please select booking</option>
                                  @forelse($booking as $data)
                                <option value="{{$data->booking_number}}">{{$data->booking_number}}</option>
                                    @empty
                                    <option value=""></option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Payslip Image</label>
                                <input type="file" class="form-control " name="payslip_image"  id="image1" accept="image/*">
                            </div>
                        </div>
                         <div class="col-md-12">
                            <div class="form-group">
                                <label>Remark</label>
                                <textarea rows="5" class="form-control " name="remark"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.payslips.index') }}" class="btn btn-danger mr-3">Cancel</a>
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
    {{-- <script>
        $('#image1').on('change', function() {
        let size = this.files[0].size; // this is in bytes
        if (size > 2000000) {
            swal("Image Size exceed than limit!", "Please rechoose back!", "error");
            document.querySelector('#image1').value = '';

        }
    });

        $('#image2').on('change', function() {
        let size = this.files[0].size; // this is in bytes
        if (size > 2000000) {
            swal("Image Size exceed than limit!", "Please rechoose back!", "error");
            document.querySelector('#image2').value = '';

        }
    });

</script> --}}
@endsection
