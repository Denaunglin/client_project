@extends('backend.admin.layouts.app')

@section('meta_title', 'Check-In Deposit')
@section('page_title', 'Check-In Deposit')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
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
                            <th>Night </th>
                            <th>Deposit %</th>
                            <th class="no-sort action">Action</th>
                            <th class="hidden">Updated at</th>
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
            ajax: `/admin/deposits`,
            columns: [{
                    data: "plus-icon",
                    name: "plus-icon",
                    defaultContent: null
                },
                {
                    data: 'night',
                    name: 'night',
                    defaultContent: "-",
                    class: ""
                },
                {
                    data: 'deposit',
                    name: 'deposit',
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
    });

</script>
@endsection
