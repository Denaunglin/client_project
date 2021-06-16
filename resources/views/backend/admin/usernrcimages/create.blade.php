@extends('backend.admin.layouts.app')

@section('meta_title', 'Add User Nrc or Passport Picture')
@section('page_title', 'Add User Nrc or Passport Picture')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.usernrcimages.store') }}" method="post" enctype="multipart/form-data" id="create">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>User</label>
                                <select name="user_id" class="form-control select2">
                                    <option value="">Please select user</option>
                                  @forelse($user as $data)
                                <option value="{{$data->id}}">{{$data->name}} / {{$data->phone}} / {{$data->address}}</option>
                                    @empty
                                    <option value=""></option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nrc or Passport Front Image</label>
                                <input type="file" class="form-control " name="front_pic"  id="image1" accept="image/*">
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label>Nrc or Passport Back Image</label>
                                <input type="file" class="form-control" name="back_pic"   id="image2"  accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.usernrcimages.index') }}" class="btn btn-danger mr-3">Cancel</a>
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
