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
                        <div class="col-lg-12">
                          
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
