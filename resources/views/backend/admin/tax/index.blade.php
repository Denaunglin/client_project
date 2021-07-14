@extends('backend.admin.layouts.app')

@section('meta_title', 'Taxes')
@section('page_title')
@lang("message.header.tax")
@endsection
@section('tax-active','mm-active')

@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('page_title_buttons')
<div class="d-flex justify-content-end">
    <a href="{{route('admin.taxes.create')}}" title="Add User" class="btn btn-primary action-btn">@lang("message.header.add_tax")</a>
</div>
@endsection
@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="align-middle table data-table" style="width:100%">
                        <thead>
                            <th></th>
                            <th>@lang("message.name") </th>
                            <th>@lang("message.header.amount")  %</th>
                            <th class="no-sort action">@lang("message.header.action") </th>
                            <th class="hidden">@lang("message.header.updated_at") </th>
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
                    'url' : '{{ url("/admin/taxes?trash=0") }}',
                    'type': 'GET',
                },
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
                    data: 'amount',
                    name: 'amount',
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
                [4, 'desc']
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
                            url :`{{url('/admin/taxes/`+id+`/')}}`,
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
