@extends('backend.admin.layouts.app')

@section('meta_title', 'Item Sub Categories')
@section('page_title', 'Item Sub Categories')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('page_title_buttons')
<div class="d-flex justify-content-end">
    <div class="custom-control custom-switch p-2 mr-3">
        <input type="checkbox" class="custom-control-input trashswitch" id="trashswitch">
        <label class="custom-control-label" for="trashswitch"><strong>Trash</strong></label>
    </div>

    @can('add_item_sub_category')
    <a href="{{route('admin.item_sub_categories.create')}}" title="Add Category" class="btn btn-primary action-btn">Add Item Sub Category
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
                            <th></th>
                            <th>Name </th>
                            <th class="no-sort action">Action</th>
                            <th class="d-none hidden">Updated at</th>
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
    $(function () {
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: `/admin/item_sub_categories?trash=0`,
            columns: [{
                    data: "plus-icon",
                    name: "plus-icon",
                    defaultContent: null
                },
                {
                    data: 'name',
                    name: 'name',
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
                [3, 'desc']
            ],
            responsive: {
                details: {
                    type: "column",
                    target: 0
                }
            },
            columnDefs: [
             
                {
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
            table.ajax.url('/admin/item_sub_categories?trash=' + trash).load();
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
                            url: '/admin/item_sub_categories/' + id + '/trash',
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
                            url: '/admin/item_sub_categories/' + id + '/restore',
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
                            url: '/admin/roomtypes/' + id,
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
