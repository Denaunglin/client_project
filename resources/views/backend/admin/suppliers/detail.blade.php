@extends('backend.admin.layouts.app')

@section('meta_title', ' User Detail')
@section('page_title', ' User Detail')
@section('page_title_icon')
<i class="metismenu-icon pe-7s-users"></i>
@endsection
@section('extra_css')
<style>
   .modal-backdrop.fade {
    opacity: 0 !important;
}
   .modal-backdrop{
       position: relative !important;
   }
   .modal-dialog{
       margin-top:100px;
   }
</style>
@endsection
@section('content')

<div class="row">
    <div class="col-md-8 offset-md-2 col-sm-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
               <div class="table-responsive">
                   <table class="table table-bordered ">
                    <thead >
                    <div class="text-center">
                       <h3> Client User Information</h3>
                    </div>     
                    </thead>   
                    <tbody>
                        <tr class="text-center">
                            @if($client_detail->userprofile)
                            <td colspan="2" >
                            <img width="300px" height="200px" src="{{$client_detail->userprofile->image_path()}}">
                            </td>
                            @else
                            @endif
                        </tr>
                        <tr>
                            <td>Name</td>
                        <td>{{$client_detail->name}}</td>
                        </tr>
                        <tr>
                            <td>Email</td>
                        <td>
                            {{$client_detail->email}}

                        </td>
                        </tr>
                        
                        <tr>
                            <td>Contact Number</td>
                        <td>{{$client_detail->phone}}</td>
                        </tr>
                        
                        <tr>
                            <td>NRC or Passport</td>
                            <td>
                                {{$client_detail->nrc_passport}}
                            </td>
                        </tr>
    
                        <tr>
                            <td>Date of Birth</td>
                            <td>
                                {{$client_detail->date_of_birth}}
                            </td>
                        </tr>

                        <tr>
                            <td>Gender</td>
                            <td>
                                {{$client_detail->gender}}
                            </td>
                        </tr>
                        
                        <tr>
                            <td>Address</td>
                            <td>
                                {{$client_detail->address}}
                            </td>
                        </tr>
                    @if($nrc_image)
                         <tr>
                            <td>
                                <b>Nrc Front Image</b>
                            </td>
                            <td>
                            <b>Nrc Back Image</b>
                            </td>
                        </tr>
                           <tr>
                            <td>
                                
                            <img src="{{$nrc_image->image_path_front()}}" width="100px" alt="" data-toggle="modal" data-target="#exampleModal1">
                            <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Nrc Front Image</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                    <img src="{{$nrc_image->image_path_front()}}" width="100%" alt="">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            </td>
                            <td>
                            <img src="{{$nrc_image->image_path_back()}}" width="100px" alt="" data-toggle="modal" data-target="#exampleModal2">
                             <div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Nrc Back Image</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                    <img src="{{$nrc_image->image_path_back()}}" width="100%" alt="">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                    </div>
                                </div>
                            </div>
                            </td>
                        </tr>
                    @endif
                        

                    </tbody>
                   </table>
               </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
{!! JsValidator::formRequest('App\Http\Requests\StoreClientUser', '#form') !!}
@endsection