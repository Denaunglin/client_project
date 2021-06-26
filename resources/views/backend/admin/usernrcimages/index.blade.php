@extends('backend.admin.layouts.app')

@section('meta_title', 'User Nrc Images')
@section('page_title', 'User Nrc Images')
@section('user-nrc-active','mm-active')

@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('page_title_buttons')
<div class="d-flex justify-content-end">
    <div class="custom-control custom-switch p-2 mr-3">
        <input type="checkbox" class="custom-control-input trashswitch" id="trashswitch">
        <label class="custom-control-label" for="trashswitch"><strong>Trash</strong></label>
    </div>

    @can('add_user_nrc_image')
    <a href="{{route('admin.usernrcimages.create')}}" title="Add Category" class="btn btn-primary action-btn">Add User
        Nrc Images</a>
    @endcan
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="align-middle table data-table" id="images">
                        <thead>
                            <tr>
                                <th></th>
                                <th>User</th>
                                <th>Nrc Front Image</th>
                                <th>Nrc Back Image</th>
                                <th>Created at</th>
                                <th>Updated at</th>
                                <th class="no-sort action">Action</th>
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
                ajax: `/admin/usernrcimages?trash=0`,
                columns: [{
                        data: "plus-icon",
                        name: "plus-icon",
                        defaultContent: null
                    },
                    {
                        data: 'user.name',
                        name: 'user.name',
                        defaultContent: "-",
                        class: ""
                    },
                       {
                        data: 'front_pic',
                        name: 'front_pic',
                        defaultContent: "-",
                        class: ""
                    },
                       {
                        data: 'back_pic',
                        name: 'back_pic',
                        defaultContent: "-",
                        class: ""
                    },
                        {
                        data: 'created_at',
                        name: 'created_at',
                        defaultContent: "-",
                        class: ""
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        defaultContent: null
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },

                ],
                order: [
                    [3, 'desc']
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
            table.ajax.url('/admin/usernrcimages?trash=' + trash).load();
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
                            url: '/admin/usernrcimages/' + id + '/trash',
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
                            url: '/admin/usernrcimages/' + id + '/restore',
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
                            url: '/admin/usernrcimages/' + id,
                            type: 'DELETE',
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
