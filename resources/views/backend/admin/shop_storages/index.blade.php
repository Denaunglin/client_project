@extends('backend.admin.layouts.app')
@section('meta_title', 'Shop Storage')
@section('page_title')
@lang("message.header.shop_storage")
@endsection
@section('shop-storage-active','mm-active')

@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('page_title_buttons')
<div class="d-flex justify-content-end">
    <div class="custom-control custom-switch p-2 mr-3">
        <input type="checkbox" class="custom-control-input trashswitch" id="trashswitch">
        <label class="custom-control-label" for="trashswitch"><strong>@lang("message.header.trash")</strong></label>
    </div>

    @can('add_item_category')
    <a href="{{route('admin.shop_storages.create')}}" title="Add Shop Storage" class="btn btn-primary action-btn">@lang("message.header.add_shop_storage")</a>
    @endcan
</div>
@endsection

@section('content')

<div class="row mb-3">
    <div class="col-md-2">
        <div class="card shadow">
            <div class="card-body">
                <table>
                    <tr>
                        <td> Item</td>
                        <td><span class="badge badge-info mr-3">{{$total_item}}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card shadow">
            <div class="card-body">
                <table>
                    <tr>
                        <td> Qty</td>
                        <td><span class="badge badge-info mr-3">{{$total_qty}}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body">
                <table>
                    <tr>
                        <td> Buying Price</td>
                        <td><span class="badge badge-info mr-3">{{number_format($total_buying)}}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-body">
                <table>
                    <tr>
                        <td> Sale (Retail) Price</td>
                        <td><span class="badge badge-info mr-3">{{number_format($total_retail)}}</span></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="align-middle table data-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>@lang("message.header.item")</th>
                                <th>@lang("message.header.qty")</th>
                                <th>Action</th>
                                <th class="d-none hidden">@lang("message.header.updated_at")</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th></th>
                                <th>@lang("message.total")</th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
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
                'excel',
                {
              text: '<i class="fas fa-file-pdf"></i> PDF',
              extend: 'pdfHtml5',
              filename: 'Shop Storage Report',
              orientation: 'portrait', //portrait
              pageSize: 'A4', //A3 , A5 , A6 , legal , letter
              exportOptions: {
                  columns: [1,2]
              },
              customize: function(doc) {
                  //Remove the title
                  doc.content.splice(0, 1);
                  var report_time = moment().format('YYYY-MM-DD HH:mm:ss');
                  doc.pageMargins = [20, 40, 20, 30];
                  doc.defaultStyle.fontSize = 6;
                  doc.defaultStyle.font = 'NotoSansMyanmar';
                  doc.styles.tableHeader.fontSize = 8;
                  doc.content[0].table.widths = '*';
                  doc.styles.tableBodyEven.alignment = 'center';
                  doc.styles.tableBodyOdd.alignment = 'center';
                  // Header
                  doc['header'] = (function() {
                      return {
                          columns: [{
                                  alignment: 'left',
                                  italics: true,
                                  text: 'Shop Storage Report',
                                  fontSize: 14,
                              },
                              {
                                  alignment: 'right',
                                  text: 'Report Time ' + report_time.toString(),
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
                    'url' : '{{ url("/admin/shop_storages?trash=0") }}',
                    'type': 'GET',
                },
                columns: [{
                        data: "plus-icon",
                        name: "plus-icon",
                        defaultContent: null
                    },
                    {
                        data: 'item_id',
                        name: 'item_id',
                        defaultContent: "-",
                        class: ""
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        defaultContent: "-",
                        class: ""
                    },
                    {
                        data: 'action',
                        name: 'action',
                        defaultContent: "-",
                        class: ""
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
                },
                footerCallback: function(row, data, start, end, display) {
                var api = this.api(),
                    data;

                // Remove the formatting to get integer data for summation
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                // Total
                total2 = api.column(2).data().reduce(function(a, b) { return intVal(a) + intVal(b); }, 0);
              
                // Update footer
                $(api.column(2).footer()).html(total2.toLocaleString());
              
        }

       
        });
            

        $(document).on('change', '.trashswitch', function () {
            if ($(this).prop('checked') == true) {
                var trash = 1;
            } else {
                var trash = 0;
            }
            table.ajax.url(`{{url('/admin/shop_storages?trash=`+trash+`/')}}`).load();
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
                            url :`{{url('/admin/shop_storages/`+id+`/trash')}}`,
                            type: 'PUT',
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
                            url :`{{url('/admin/shop_storages/`+id+`/restore')}}`,
                            type: 'PUT',
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
                            url :`{{url('/admin/shop_storages/`+id+`/delete')}}`,
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
