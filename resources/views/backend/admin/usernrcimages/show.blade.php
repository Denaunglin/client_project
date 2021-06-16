@extends('backend.admin.layouts.app')

@section('meta_title', 'User NRC or Passport')
@section('page_title', 'User NRC or Passport')

@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <div id="images" class="mb-3">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="p-3 border text-center">
                                <img src="{{$usernrcimage->image_path_front()}}" alt="" style="width:200px;">
                                <p>NRC or Passport Front</p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="p-3 border text-center">
                                <img src="{{$usernrcimage->image_path_back()}}" alt="" style="width:200px;">
                                <p>NRC or Passport Back</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-2">
                            <strong><i class="fab fa-gg mr-1"></i> User Name</strong>
                            <p class="text-muted">{{$usernrcimage->user ? $usernrcimage->user->name : '-'}}</p>
                        </div>
                        <div class="mb-2">
                            <strong><i class="fab fa-gg mr-1"></i> User Email</strong>
                            <p class="text-muted">{{$usernrcimage->user ? $usernrcimage->user->email : '-'}}</p>
                        </div>
                        <div class="mb-2">
                            <strong><i class="fab fa-gg mr-1"></i> User Phone</strong>
                            <p class="text-muted">{{$usernrcimage->user ? $usernrcimage->user->phone : '-'}}</p>
                        </div>
                        <div class="mb-2">
                            <strong><i class="fab fa-gg mr-1"></i> Created at</strong>
                            <p class="text-muted">{{$usernrcimage->created_at}}</p>
                        </div>
                        <div class="mb-2">
                            <strong><i class="fab fa-gg mr-1"></i> Updated at</strong>
                            <p class="text-muted">{{$usernrcimage->updated_at}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(document).ready(function(){
        new Viewer(document.getElementById('images'));
    });
</script>
@endsection
