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
                            {{$client_detail->email ? $client_detail->email : 'No email added' }}

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