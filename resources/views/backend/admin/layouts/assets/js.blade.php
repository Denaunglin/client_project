<script>
    var PREFIX_URL = '{{config("app.prefix_admin_url")}}';
    var CSRF_TOKEN = '{{csrf_token()}}';
</script>

{{-- <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script> --}}
<script src="{{asset('assets/js/jquery.min.js')}}"></script>

{{-- <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script> --}}
<script src="{{ asset('vendor/assets/scripts/main.js') }}"></script>
{{-- <script src="{{ asset('vendor/assets/scripts/script.js') }}"></script> --}}
 
{{-- Datatable --}}
{{-- <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script> --}}
<script src="{{asset('assets/dataTable/datatable.min.js')}}"></script>

<script src="//cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.min.js"></script>

{{-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/js/bootstrap.min.js">
</script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.18.0/js/mdb.min.js"></script> --}}

{{-- <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script> --}}
<script src="{{asset('assets/dataTable/datatable_bootstrap4.min.js')}}"></script>

{{-- <script src="https://cdn.datatables.net/fixedcolumns/3.2.6/js/dataTables.fixedColumns.min.js"></script> --}}
<script src="{{asset('assets/dataTable/datatable_fixedcolumns.min.js')}}"></script>

{{-- <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script> --}}
<script src="{{asset('assets/dataTable/datatable_botton.min.js')}}"></script>

{{-- <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.bootstrap4.min.js"></script> --}}
<script src="{{asset('assets/dataTable/button_bootstrap.min.js')}}"></script>

{{-- <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script> --}}
<script src="{{asset('assets/dataTable/button_flash.min.js')}}"></script>

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> --}}
<script src="{{asset('assets/dataTable/jszip.min.js')}}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
{{-- <script src="{{asset('assets/dataTable/pdfmake.min.js')}}"></script> --}}
<script src="{{asset('fonts/vendor/vfsfont.js')}}"></script>


{{-- <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script> --}}
<script src="{{asset('assets/dataTable/button_html.min.js')}}"></script>


<!-- Daterange picker -->
{{-- <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script> --}}
<script src="{{asset('assets/daterangepicker/moment.js')}}"></script>

{{-- <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> --}}
<script src="{{asset('assets/daterangepicker/daterangepicker.js')}}"></script>


{{-- Dropzone --}}
{{-- <script src='https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/dropzone.js'></script> --}}
<script src="{{asset('assets/dropzone/dropzone.js')}}"></script>

<!-- Laravel Javascript Validation -->
<script src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>

<!-- Sweet Alert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.4.0/perfect-scrollbar.min.js"></script> --}}
<script src="{{asset('assets/scrollbar/scrollbar.js')}}"></script>

{{-- Image Viewer --}}
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.3.6/viewer.js"></script> --}}
<script src="{{asset('assets/imageviewer/viewer.js')}}"></script>


@include('layouts.assets.select2')
<script>
    let token = document.head.querySelector('meta[name="csrf-token"]');
    if (token) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': token.content
            }
        });
    } else {
        console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
    }

    (function ($) {
            const ps = new PerfectScrollbar('.scrollbar-sidebar');

            $('.previous-btn').on('click', function(){
                window.history.go(-1);
                return false;
            });

            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });

            @if(session('success'))
            Toast.fire({
                type: 'success',
                title: '{{ session("success") }}'
            })
            @php
            Session::forget('success');
            @endphp
            @endif

            @if(session('error'))
            Toast.fire({
                type: 'error',
                title: '{{ session("error") }}'
            })
            @php
            Session::forget('error');
            @endphp
            @endif

            $.fn.dataTable.ext.buttons.refresh = {
                text: '<i class="fa fa-redo"></i> Refresh',
                attr: { class: 'btn btn-success'},
                action: function ( e, dt, node, config ) {
                    dt.clear().draw();
                    dt.ajax.reload();
                }
            };
        })($);
</script>
@yield('script')
