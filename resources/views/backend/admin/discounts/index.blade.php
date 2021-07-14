@extends('backend.admin.layouts.app')

@section('meta_title', 'Discount')
@section('page_title')
@lang("message.header.discount")
@endsection
@section('discount-active','mm-active')

@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('page_title_buttons')
<div class="d-flex justify-content-end">
    <div class="custom-control custom-switch p-2 mr-3">
        <input type="checkbox" class="custom-control-input trashswitch" id="trashswitch">
        <label class="custom-control-label" for="trashswitch"><strong>@lang("message.header.trash")</strong></label>
    </div>

    @can('add_discount')
    <a href="{{route('admin.discounts.create')}}" title="Add Disocunt" class="btn btn-primary action-btn">@lang("message.header.add_discount")
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
                                <th> @lang("message.header.customer_type")</th>
                                <th> @lang("message.header.item")</th>
                                <th>@lang("message.header.discount_percentage") </th>
                                <th>@lang("message.header.discount_amount")</th>
                                <th class="no-sort action">@lang("message.header.action")</th>
                                <th class="d-none hidden">@lang("message.header.updated_at")</th>
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
{!! JsValidator::formRequest('App\Http\Requests\DiscountsRequest', '#form') !!}

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
                    'url' : '{{ url("/admin/discounts?trash=0") }}',
                    'type': 'GET',
                },
                columns: [{
                        data: "plus-icon",
                        name: "plus-icon",
                        defaultContent: null
                    },
                    {
                        data: 'customer',
                        name: 'customer',
                        defaultContent: "-",
                        class: ""
                    },
                    {
                        data: 'item',
                        name: 'item',
                        defaultContent: "-",
                        class: ""
                    },
                    {
                        data: 'discount_percentage',
                        name: 'discount_percentage',
                        defaultContent: "-",
                        class: ""
                    },
                   
                    {
                        data: 'discount_amount',
                        name: 'discount_amount',
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
                    [5, 'desc']
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
            table.ajax.url(`{{url('/admin/discounts?trash=`+trash+`/')}}`).load();
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
                            url :`{{url('/admin/discounts/`+id+`/trash')}}`,
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
                            url :`{{url('/admin/discounts/`+id+`/restore')}}`,  
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
                            url :`{{url('/admin/discounts/`+id+`/')}}`,
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
