@extends('backend.admin.layouts.app')

@section('meta_title', 'Commodity Sales Items')
@section('page_title')
@lang("message.header.selling_item")
@endsection
@section('commodity-sale-active','mm-active')

@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('page_title_buttons')
<div class="d-flex justify-content-end">
    <div class="custom-control custom-switch p-2 mr-3">
        <input type="checkbox" class="custom-control-input trashswitch" id="trashswitch">
        <label class="custom-control-label" for="trashswitch"><strong>@lang("message.header.trash")</strong></label>
    </div>
    @can('add_item')
    <a href="{{route('admin.sell_items.create_retail')}}" title="Add Category" class="btn btn-primary action-btn">Add Retail Sales Item</a>
    @endcan
    @can('add_item')
    <a href="{{route('admin.sell_items.create_wholesale')}}" title="Add Category" class="btn btn-primary ml-2 action-btn">Add Wholesale Sales Item</a>
    @endcan
</div>
@endsection

@section('content')
<div class="pb-3">
    <div class="row">
        <div class="col-md-6 col-sm-12 col-xl-3">
            <div class="d-inline-block mb-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar-alt mr-1"></i> @lang("message.date") : </span>
                    </div>
                    <input type="text" class="form-control datepicker" placeholder="All">
                </div>
            </div>
        </div>  
             
    </div>   
    </div>
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="align-middle m-0 table data-table">
                        <thead>
                            <tr>
                                <th class="hidden"></th>
                                <th>Total Item</th>
                                <th>Customer</th>
                                <th>Total Qty</th>
                                <th>Total Amount</th>
                                <th>Discount</th>
                                <th>Paid Amount</th>
                                <th>Credit Amount</th>
                                <th>Paid Status</th>
                                <th>Sell Type</th>
                                <th class="no-sort action">@lang("message.header.action")</th>
                                <th class="d-none hidden">@lang("message.header.updated_at")</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
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
    var route_model_name = "sell_items";
        var app_table;
        $(function() {
            app_table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                dom: 'Bfrtip',
                buttons: [
                    'excel',
                    {
              text: '<i class="fas fa-file-pdf"></i> PDF',
              extend: 'pdfHtml5',
              filename: 'Commodity Sales Report',
              orientation: 'portrait', //portrait
              pageSize: 'A4', //A3 , A5 , A6 , legal , letter
              exportOptions: {
                  columns: [1,2,3,4,5,6,7,8]
              },
              customize: function(doc) {
                  //Remove the title
                  doc.content.splice(0, 1);
                  var report_time = moment().format('YYYY-MM-DD HH:mm:ss');
                  doc.pageMargins = [20, 40, 20, 30];
                  doc.defaultStyle.fontSize = 6;
                  doc.defaultStyle.font = 'NotoSansMyanmar';
                  doc.styles.tableHeader.fontSize = 6;
                  doc.content[0].table.widths = '*';
                  doc.styles.tableBodyEven.alignment = 'center';
                  doc.styles.tableBodyOdd.alignment = 'center';
                  // Header
                  doc['header'] = (function() {
                      return {
                          columns: [{
                                  alignment: 'left',
                                  italics: true,
                                  text: 'Commodity Sales Report',
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
                    // {
                    //     extend: 'refresh'
                    // },
                    {
                        extend: 'pageLength'
                    }
                ],
                lengthMenu: [
                    [10, 25, 50, 100, 500],
                    ['10 rows', '25 rows', '50 rows', '100 rows', '500 rows']
                ],
                ajax: {
                    'url' : '{{ url("/admin/sell_items?trash=0") }}',
                    'type': 'GET',
                },
                columns: [
                    {data: 'plus-icon', name: 'plus-icon', defaultContent: "-", class: ""},
                    {data: 'total_item', name: 'item_id', defaultContent: "-", class: ""},
                    {data: 'customer', name: 'customer', defaultContent: "-", class: ""},
                    {data: 'total_qty', name: 'item_category', defaultContent: "-", class: ""},
                    {data: 'total_amount', name: 'item_sub_category', defaultContent: "-", class: ""},
                    {data: 'discount', name: 'discount', defaultContent: "-", class: ""},
                    {data: 'paid_amount', name: 'qty', defaultContent: "-", class: ""},
                    {data: 'credit_amount', name: 'price', defaultContent: "-", class: ""},
                    {data: 'paid_status', name: 'discount', defaultContent: "-", class: ""},
                    {data: 'sell_type', name: 'sell_type', defaultContent: "-", class: ""},
                    {data: 'action', name: 'action', orderable: false, searchable: false, class: "action"},
                    {data: 'updated_at', name: 'updated_at', defaultContent: null}
                    ],
                    order: [
                        [10, 'desc']
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
                // total1 = api.column(1).data().reduce(function(a, b) { return intVal(a) + intVal(b); }, 0);
                total3 = api.column(3).data().reduce(function(a, b) { return intVal(a) + intVal(b); }, 0);
                total4 = api.column(4).data().reduce(function(a, b) { return intVal(a) + intVal(b); }, 0);
                total5 = api.column(5).data().reduce(function(a, b) { return intVal(a) + intVal(b); }, 0);
                total6 = api.column(6).data().reduce(function(a, b) { return intVal(a) + intVal(b); }, 0);
                total7 = api.column(7).data().reduce(function(a, b) { return intVal(a) + intVal(b); }, 0);
                // total8 = api.column(8).data().reduce(function(a, b) { return intVal(a) + intVal(b); }, 0);

                // Update footer
                // $(api.column(1).footer()).html(total1.toLocaleString());
                $(api.column(3).footer()).html(total3.toLocaleString());
                $(api.column(4).footer()).html(total4.toLocaleString());
                $(api.column(5).footer()).html(total5.toLocaleString());
                $(api.column(6).footer()).html(total6.toLocaleString());
                $(api.column(7).footer()).html(total7.toLocaleString());
                // $(api.column(8).footer()).html(total8.toLocaleString());

        }
            }); 
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
            var trash = $('.trashswitch').prop('checked') ? 1 : 0;
            app_table.ajax.url(`{{url('/admin/sell_items?daterange=`+daterange+`&trash=`+trash+`/')}}`).load();
        });

        $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');

            var daterange = $('.datepicker').val();
            var status = $('.status').val();
            var payment_status = $('.payment_status').val();
            var trash = $('.trashswitch').prop('checked') ? 1 : 0;
            app_table.ajax.url(`{{url('/admin/sell_items?daterange=`+daterange+`&trash=`+trash+`/')}}`).load();
        }); 

        $(document).on('change', '.item', function() {
                var booking_user_name = $('#booking_user_name').val();
                var item = $('.item').val();
                var trash = $('.trashswitch').prop('checked') ? 1 : 0;
                app_table.ajax.url(`{{url('/admin/sell_items?item=`+item+`&trash=`+trash+`/')}}`).load();
        });

</script>
@include('backend.admin.layouts.assets.trash_script')
@endsection
