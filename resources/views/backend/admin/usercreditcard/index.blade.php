@extends('backend.admin.layouts.app')

@section('meta_title', 'User Credit Card')
@section('page_title', 'User Credit Card')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('page_title_buttons')
<div class="d-flex justify-content-end">
    <div class="custom-control custom-switch p-2 mr-3">
        <input type="checkbox" class="custom-control-input trashswitch" id="trashswitch">
        <label class="custom-control-label" for="trashswitch"><strong>Trash</strong></label>
    </div>
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
                            <th>User name</th>
                            <th>Credit Card Type</th>
                            <th>Account Name</th>
                            <th>Credit Number</th>
                            <th>Expire Date</th>
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
            dom: 'Bfrtip',
            buttons: [
                 {
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    extend: 'pdfHtml5',
                    filename: 'User Credit Card Report',
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
                                        text: 'User Credit Card Report',
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
                    extend: 'refresh'
                },
                {
                    extend: 'pageLength'
                }
            ],
            ajax: `/admin/usercreditcards?trash=0`,
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
                    data: 'credit_type',
                    name: 'credit_type',
                    defaultContent: "-",
                    class: ""
                },
                {
                    data: 'account_name',
                    name: 'account_name',
                    defaultContent: "-",
                    class: ""
                },
                {
                    data: 'credit_no',
                    name: 'credit_no',
                    defaultContent: "-",
                    class: ""
                },
                {
                    data: 'expire_date',
                    name: 'expire_date',
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
                [7, 'desc']
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
            table.ajax.url('/admin/usercreditcards?trash=' + trash).load();
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
                            url: '/admin/usercreditcards/' + id + '/trash',
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
                            url: '/admin/usercreditcards/' + id + '/restore',
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
                            url: '/admin/messages/' + id,
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
