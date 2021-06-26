@extends('backend.admin.layouts.app')

@section('meta_title', 'Payslips')
@section('page_title', 'Payslips')
@section('payslip-active','mm-active')

@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('page_title_buttons')
<div class="d-flex justify-content-end">
    <div class="custom-control custom-switch p-2 mr-3">
        <input type="checkbox" class="custom-control-input trashswitch" id="trashswitch">
        <label class="custom-control-label" for="trashswitch"><strong>Trash</strong></label>
    </div>

    @can('add_payslip')
    <a href="{{route('admin.payslips.create')}}" title="Add Payslip" class="btn btn-primary action-btn">Add Payslip</a>
    @endcan
</div>
@endsection

@section('content')

<div class="row">
       <div class="col-md-6 col-sm-12 col-xl-3">
        <div class="d-inline-block mb-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar-alt mr-1"></i> Payslip Date : </span>
                    </div>
                    <input type="text" class="form-control datepicker" placeholder="All">
                </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12 col-xl-3">
        <div class="d-inline-block mb-5" style="width:100%">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-data mr-1"></i> Booking Number : </span>
                    </div>
                    <select class="custom-select booking_number">
                        <option value="">All</option>
                        @foreach($payslips_filter as $data)
                        
                        <option value="{{$data->booking_no}}">{{$data->booking_no}}</option>
                        @endforeach
                    </select>
                </div>
        </div>
    </div>
 
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="align-middle table data-table" id="images">
                        <thead>
                            <tr>
                                <th></th>
                                <th> 
                                    <div class="row">
                                        <div class="col-md-3">
                                            <span>Payslip</span>
                                             <span class="ml-5" > Booking NO</span> 
                                        </div>
                                        <div class="col-md-3">
                                            <span >Uploaded Date</span> 
                                        </div>
                                         <div class="col-md-3">
                                            <span >Status</span> 
                                        </div>
                                        <div class="col-md-3">
                                            <span >Action</span>                              
                                        </div>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $(function() {
            var route_model_name = "payslips";
            var booking_number = $('.booking_number').val();
            var daterange = $('.datepicker').val();
            var viewer = new Viewer(document.getElementById('images'));
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'refresh'
                    },
                    {
                        extend: 'pageLength'
                    }
                ],
                lengthMenu: [
                    [10, 25, 50, 100, 500],
                    ['10 rows', '25 rows', '50 rows', '100 rows', '500 rows']
                ],
                ajax: {
                    'url' :'{{ url("/admin/payslips?trash=0") }}',
                    'type': 'GET',
                },
                columns: [{
                        data: "plus-icon",
                        name: "plus-icon",
                        defaultContent: null
                    },
                  
                     {
                        data: 'Payslip',
                        name: 'Payslip',
                        defaultContent: "-",
                        class: ""
                    },

                ],
                order: [
                    [1, 'desc']
                ],
                responsive: {
                    details: {
                        type: "column",
                        target: 0
                    }
                },
                columnDefs: [{
                        targets: "no-sort",
                        orderable: false
                    },
                    {
                        className: "control",
                        orderable: false,
                        targets: 0
                    },
                    {
                        targets: "hidden",
                        visible: false
                    }
                ],
                pagingType: "simple_numbers",
                language: {
                    paginate: {
                        previous: "«",
                        next: "»"
                    },
                    processing: `<div class="processing_data">
                        <div class="spinner-border text-info" role="status">
                            <span class="sr-only">Loading...</span>
                        </div></div>`
                },
                drawCallback: function(){
                    viewer.destroy();
                    viewer = new Viewer(document.getElementById('images'));
                }
            });

        $(document).on('change', '.trashswitch', function () {
            if ($(this).prop('checked') == true) {
                var trash = 1;
            } else {
                var trash = 0;
            }
            app_table.ajax.url(`{{url('/admin/payslips?trash=`+trash+`/')}}`).load();

        });

        $(document).on('click', '.trash', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            swal("Are you sure, you want to trash?", {
                    className: "danger-bg",
                    buttons: [true, "Yes"],
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url :`{{url('/admin/payslips/`+id+`/trash')}}`,
                            type: 'GET',
                            success: function () {
                                table.ajax.reload();
                            }
                        });
                    }
                });
        });

         $(document).on('click', '.change_status', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            var status = $(this).data('status');
            var remark = $(this).data('remark');
            var actions = ''; 

            if (status == 0) {
                actions = `<option value="0">Approve</option>
                <option value="1">Comfirmed</option>
                <option value="2">Canceled</option>
                <option value="3">Completed</option>`;
            } else if (status == 1) {
              actions = `<option value="1">Comfirmed</option>
                <option value="0">Approve</option>
                <option value="2">Canceled</option>
                <option value="3">Completed</option>`;
            } else if (status == 2) {
                 actions = `<option value="2">Canceled</option>
                <option value="0">Approve</option>
                <option value="1">Comfirmed</option>
                <option value="3">Completed</option>`;
            }else if (status == 2) {
                 actions = `<option value="3">Completed</option>
                 <option value="2">Canceled</option>
                <option value="0">Approve</option>
                <option value="1">Comfirmed</option>`;
            }


            Swal.fire({
                title: `Change Status`,
                customClass: {
                    container: 'status-modal'
                },
                html: `<div class="form-group my-3">
                    <label>Remark</label>
                    <textarea name="remark" class="form-control remark">${remark}</textarea>
                </div>
                <div class="form-group mb-3">
                    <label>Status</label>
                    <select class="form-control status">
                    ${actions}
                    </select>
                </div>`,
                showCancelButton: true,
                reverseButtons: true,
                focusConfirm: false,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
            })
            .then((result) => {
                if (result.value) {
                    var remark = $('.status-modal .remark').val();
                    var status = $('.status-modal .status').val();

                    $.ajax({
                        url: `/admin/payslips/${id}/change_status?remark=${remark}&status=${status}`,
                        type: 'GET',
                        success: function(response) {
                            if(response.result == 1) {
                                table.ajax.reload();
                                swal({
                                    icon: "success",
                                    title: "Success",
                                    text: response.message,
                                });
                            } else {
                                swal({
                                    icon: 'error',
                                    title: "Something wrong",
                                    text: response.message
                                });
                            }
                        },error: function(res) {
                            swal({
                                icon: 'error',
                                title: "Something wrong",
                                text: res.responseJSON.message
                            });
                        }
                    });
                }
            });
        });


          $(document).on('click', '.markasread', function(e) {
            e.preventDefault();
            var id = $(this).data('id');

            $.ajax({
                url: '/admin/payslips/' + id + '/mark-as-read',
                type: 'GET',
                success: function() {
                    table.ajax.reload();
                }
            });
        });


        $(document).on('click', '.restore', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            swal("Are you sure, you want to restore?", {
                    className: "danger-bg",
                    buttons: [true, "Yes"],
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url :`{{url('/admin/payslips/`+id+`/restore')}}`,
                            type: 'GET',
                            success: function () {
                                table.ajax.reload();
                            }
                        });
                    }
                });
        });

        $(document).on('click', '.destroy', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            swal("Are you sure, you want to delete?", {
                    className: "danger-bg",
                    buttons: [true, "Yes"],
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url :`{{url('/admin/payslips/`+id+`/')}}`,

                            type: 'DELETE',
                            success: function () {
                                table.ajax.reload();
                            }
                        });
                    }
                });
        });

        $(document).on('change', '.booking_number', function() {
        var booking_number = $('.booking_number').val();
        var daterange = $('.datepicker').val();
        var trash = $('.trashswitch').prop('checked') ? 1 : 0;
        table.ajax.url(`/admin/payslips?booking_number=${booking_number}&daterange=${daterange}&trash=${trash}`).load();
    });

     $(".datepicker").daterangepicker({
                opens: "right",
                alwaysShowCalendars: true,
                autoUpdateInput: false,
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                locale: {
                    cancelLabel: 'Clear',
                    format: 'YYYY-MM-DD',
                    separator: " , ",
                }
            });

        $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' , ' + picker.endDate.format('YYYY-MM-DD'));
            var daterange = $('.datepicker').val();
            var booking_number = $('.booking_number').val();
            table.ajax.url(`${PREFIX_URL}/admin/${route_model_name}?daterange=${daterange}&booking_number=${booking_number}`).load();
        });

        $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            var daterange = $('.datepicker').val();
            var booking_number = $('.booking_number').val();
            table.ajax.url(`${PREFIX_URL}/admin/${route_model_name}?daterange=${daterange}&booking_number=${booking_number}`).load();
        }); 

    });

       
</script>
@endsection
