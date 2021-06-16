
@extends('backend.admin.layouts.app')
@section('meta_title', 'Upload Gallery')
@section('page_title', 'UPload Gallery')
@section('page_title_icon')

    <i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('extra_css')
<style>
    .dropzone .dz-preview .dz-image img {
    display: block;
    width: 100%;
    height: 120px;
}
    </style>
@endsection
@section('content')
    @include('layouts.errors_alert')

    <div class="page-wrapper chiller-theme toggled">
        <!-- sidebar-wrapper  -->
        <main class="page-content">
            {{-- <div class="container" id="back_to">
                <div class="row">
                    <div class="col-md-12">
                      <a class="btn btn-primary mb-5" href="{{url('/admin/rooms')}}">BACK TO Rooms</a>
                    </div>                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  
                </div>
            </div> --}}
            <div class="container-fluid">
                <div class="table-responsive">

                    <form action="{{ url('admin/center/files/'.$id) }}" class="dropzone dropzone-file-area" id="my-awesome-dropzone" method="post" enctype="multipart/form-data">
                        @csrf
                        <h3 class="sbold text-center">Drop files here or click to upload <span class="text-danger"></span></h3>
                        <p  class="text-center">Image size should be <span class="text-danger"> under 2 MB</span> </p>
                    </form>
                </div><!--end of .table-responsive-->
            </div>
        </main>
        <!-- page-content" -->
    </div>
@endsection
@section('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>

        
            Dropzone.options.myAwesomeDropzone = {

                addRemoveLinks: true,
                acceptedFiles: ".png,.jpg,.gif",
                maxFiles: 2,
                maxFilesize: 2,//max file size in MB,
               

                init: function () {
                    this.on("error", function (file, message) {
                         swal(message);
                        this.removeFile(file);
                    }); 
                    thisDropzone = this;
                    $.get('{{ url("admin/center/files/".$id) }}', function(data) {
                        $.each(data, function (key, value) {
                            var mockFile = {name: value.name , size : value.size , id : value.id };

                            thisDropzone.options.addedfile.call(thisDropzone, mockFile);

                            thisDropzone.options.thumbnail.call(thisDropzone, mockFile, value.file );
                            thisDropzone.emit("complete", mockFile);
                        });
                    });
                },

                
                dictRemoveFileConfirmation: 'Are you sure!',

                addRemoveLinks: true,

                removedfile : function (file) {
                    var id = file.id;
                    $.ajax({
                        type: 'DELETE',
                        url: '{{ url("admin/center/files") }}/'+id,
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'id': id
                        },
                        dataType: 'json'
                    });
                    var _ref;
                    return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                }

                
                
            };
                
    </script>
@endsection

