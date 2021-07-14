@extends('backend.admin.layouts.app')

@section('meta_title', 'Bussiness Info')
@section('page_title')
@lang("message.header.bussiness_info")
@endsection
@section('bussiness-info-active','mm-active')

@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('page_title_buttons')
<div class="d-flex justify-content-end">
    <div class="custom-control custom-switch p-2 mr-3">
        <input type="checkbox" class="custom-control-input trashswitch" id="trashswitch">
        <label class="custom-control-label" for="trashswitch"><strong>@lang("message.header.trash")
        </strong></label>
    </div>

    @can('add_payment_card')
    <a href="{{route('admin.bussiness_infos.create')}}" title="Add Category" class="btn btn-primary action-btn">@lang("message.header.add_bussiness_info")
    </a>
    @endcan
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="align-middle table data-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>@lang("message.header.logo")
                                </th>
                                <th>@lang("message.header.bussiness_name")
                                </th>
                                <th>@lang("message.header.bussiness_type")
                                </th>
                                <th>@lang("message.address")
                                </th>
                                <th>@lang("message.email")
                                </th>
                                <th>@lang("message.phone")
                                </th>
                                
                                <th class="no-sort action">@lang("message.header.action")
                                </th>
                                <th class="d-none hidden">@lang("message.header.updated_at")
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
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                dom: 'Bfrtip',
                buttons: [
                   
                    {
                        extend: 'pageLength'
                    }
                ],
                lengthMenu: [
                    [10, 25, 50, 100, 500],
                    ['10 rows', '25 rows', '50 rows', '100 rows', '500 rows']
                ],
                ajax: {
                    'url' : '{{ url("/admin/bussiness_infos?trash=0") }}',
                    'type': 'GET',
                },
                columns: [{
                        data: "plus-icon",
                        name: "plus-icon",
                        defaultContent: null
                    },
                    {
                        data: 'logo',
                        name: 'logo',
                        defaultContent: "-",
                        class: ""
                    },
                    {
                        data: 'name',
                        name: 'name',
                        defaultContent: "-",
                        class: ""
                    },
                    {
                        data: 'type',
                        name: 'type',
                        defaultContent: "-",
                        class: ""
                    },
                    {
                        data: 'address',
                        name: 'address',
                        defaultContent: "-",
                        class: ""
                    }, 
                    {
                        data: 'email',
                        name: 'email',
                        defaultContent: "-",
                        class: ""
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                        defaultContent: "-",
                        class: ""
                    },
                   
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        defaultContent: null
                    }
                ],
                order: [
                    [8, 'desc']
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
                }
            });

        $(document).on('change', '.trashswitch', function () {
            if ($(this).prop('checked') == true) {
                var trash = 1;
            } else {
                var trash = 0;
            }
            table.ajax.url(`{{url('/admin/bussiness_infos?trash=`+trash+`/')}}`).load();

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
                            url :`{{url('/admin/bussiness_infos/`+id+`/trash')}}`,
                            type: 'GET',
                            success: function () {
                                table.ajax.reload();
                            }
                        });
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
                            url :`{{url('/admin/bussiness_infos/`+id+`/restore')}}`,
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
                            url :`{{url('/admin/bussiness_infos/`+id+`/')}}`,
                            type: 'GET',
                            success: function () {
                                table.ajax.reload();
                            }
                        });
                    }
                });
        });
    });
</script>
@endsection
