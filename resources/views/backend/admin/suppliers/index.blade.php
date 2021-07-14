@extends('backend.admin.layouts.app')

@section('meta_title', 'Suppliers')
@section('page_title')
@lang("message.header.supplier")
@endsection
@section('supplier-active','mm-active')

@section('page_title_icon')
<i class="metismenu-icon pe-7s-users"></i>
@endsection

@section('page_title_buttons')
<div class="d-flex justify-content-end">
    <div class="custom-control custom-switch p-2 mr-3">
        <input type="checkbox" class="custom-control-input trashswitch" id="trashswitch">
        <label class="custom-control-label" for="trashswitch"><strong>@lang('message.header.trash')</strong></label>
    </div>

    @can('add_user')
    <a href="{{route('admin.suppliers.create')}}" title="Add User" class="btn btn-primary action-btn">@lang('message.header.add_supplier')</a>
    @endcan
</div>
@endsection

@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="align-middle mb-0 table table-hover data-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>@lang('message.name')</th>
                                <th>@lang('message.phone')</th>
                                <th>@lang('message.address')</th>
                                <th class="no-sort action">@lang('message.header.action')</th>
                                <th class="d-none hidden">@lang('message.updated')</th>
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
    var route_model_name = "suppliers";
    var app_table;
    $(function() {
        var route_model_name = "suppliers";
        app_table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            dom: 'Bfrtip',
                buttons: [
                      {
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    extend: 'pdfHtml5',
                    filename: 'Supplier Report',
                    orientation: 'portrait', //portrait
                    pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                    exportOptions: {
                        columns: [1,2,3,5]
                    },
                    customize: function(doc) {
                        //Remove the title
                        doc.content.splice(0, 1);
                        var report_time = moment().format('YYYY-MM-DD HH:mm:ss');
                        doc.pageMargins = [20, 60, 20, 30];
                        doc.defaultStyle.fontSize = 9;
                        doc.defaultStyle.font = 'NotoSansMyanmar';
                        doc.styles.tableHeader.fontSize = 10;
                        doc.content[0].table.widths = '*';

                        // Header
                        doc['header'] = (function() {
                            return {
                                columns: [{
                                        alignment: 'left',
                                        italics: true,
                                        text: 'Supplier Report',
                                        fontSize: 14,
                                    },
                                    {
                                        alignment: 'right',
                                        text: 'Report Time' + report_time.toString(),
                                        fontSize: 10
                                    },
                                ],
                                margin: [20,10]
                            }
                        });

                        // Footer
                        var now = new Date();
                        var jsDate = now.getDate() + '-' + (now.getMonth() + 1) + '-' + now.getFullYear(); // Format is dd-mm-yyyy
                        doc['footer'] = (function(page, pages) {
                            return {
                                columns: [
                                    {
                                        alignment: 'right',
                                        text: ['page ', {
                                            text: page.toString()
                                        }, ' of ', {
                                            text: pages.toString()
                                        }]
                                    }
                                ],
                                margin: 20
                            }
                        });

                        // Body layout
                        var objLayout = {};
                        objLayout['hLineWidth'] = function(i) {
                            return .5;
                        };
                        objLayout['vLineWidth'] = function(i) {
                            return .5;
                        };
                        objLayout['hLineColor'] = function(i) {
                            return '#aaa';
                        };
                        objLayout['vLineColor'] = function(i) {
                            return '#aaa';
                        };
                        objLayout['paddingLeft'] = function(i) {
                            return 4;
                        };
                        objLayout['paddingRight'] = function(i) {
                            return 4;
                        };
                        doc.content[0].layout = objLayout;
                    }
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
                    'url' : '{{ url("/admin/suppliers?trash=0") }}',
                    'type': 'GET',
                },
            columns: [
                {data: "plus-icon", name: "plus-icon", defaultContent: null},
                {data: 'name', name: 'name', defaultContent: "-", class: ""},
                {data: 'phone', name: 'phone', defaultContent: "-", class: ""},
                {data: 'address', name: 'address' , defaultContent: "-", class:""},
                {data: 'action', name: 'action', orderable: false, searchable: false},
                {data: 'updated_at', name: 'updated_at', defaultContent: null}
            ],
            order: [
                [3, 'desc']
            ],
            responsive: {
                details: {type: "column", target: 0}
            },
            columnDefs: [
                {targets: "no-sort", orderable: false},
                {className: "control", orderable: false, targets: 0},
                {targets: "hidden", visible: false}
            ],
            pagingType: "simple_numbers",
            language: {
                paginate: {previous: "«", next: "»"},
                processing: `<div class="processing_data">
                    <div class="spinner-border text-info" role="status">
                        <span class="sr-only">Loading...</span>
                    </div></div>`
            }
        });

    });
    
</script>
@include('backend.admin.layouts.assets.trash_script')
@endsection
