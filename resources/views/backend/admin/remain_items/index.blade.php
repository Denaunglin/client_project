@extends('backend.admin.layouts.app')

@section('meta_title', 'Remaning Items')
@section('page_title')
@lang("message.header.remaining_item_list")
@endsection
@section('remaining-item-active','mm-active')

@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('page_title_buttons')
<div class="d-flex justify-content-end">
    <div class="custom-control custom-switch p-2 mr-3">
        <input type="checkbox" class="custom-control-input trashswitch" id="trashswitch">
        <label class="custom-control-label" for="trashswitch"><strong>@lang("message.header.trash")</strong></label>
    </div>
</div>
@endsection

@section('content')
<div class="pb-3">
    <div class="row">
        <div class="col-md-6 col-sm-12 col-xl-3">
                <div class="d-inline-block mb-2 " style="width:100%">
                    <div class="input-group" >
                        <div class="input-group-prepend"><span class="input-group-text">@lang("message.header.item_name") : </span></div>
                        <select class="custom-select item mr-1" >
                            <option value="">@lang("message.header.all")</option>
                            @forelse($item as $data)
                            <option value="{{$data->id}}">{{$data->name}}</option>
                            @empty
                            <option value="">@lang("message.header.there_is_no_data")!</option>
                            @endforelse
                        </select>
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
                                <th>@lang("message.header.item_name") </th>
                                <th>@lang("message.header.item_category")  <br></th>
                                <th>@lang("message.header.item_sub_category")  </th>
                                <th>@lang("message.header.remain_qty") </th>
                                <th>@lang("message.header.price")</th>
                                <th class="d-none hidden">@lang("message.header.updated_at")</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>@lang("message.header.remain_qty")</th>
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
    var route_model_name = "remain_items";
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
              filename: 'Remaining Item Data Report',
              orientation: 'portrait', //portrait
              pageSize: 'A4', //A3 , A5 , A6 , legal , letter
              exportOptions: {
                  columns: [1,2,3,4,5,6]
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
                                  text: 'Remain Items Data Report',
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
                    'url' : '{{ url("/admin/remain_items?trash=0") }}',
                    'type': 'GET',
                },
                columns: [
                    {data: 'plus-icon', name: 'plus-icon', defaultContent: "-", class: ""},
                    {data: 'item_id', name: 'item_id', defaultContent: "-", class: ""},
                    {data: 'item_category', name: 'item_category', defaultContent: "-", class: ""},
                    {data: 'item_sub_category', name: 'item_sub_category', defaultContent: "-", class: ""},
                    {data: 'qty', name: 'qty', defaultContent: "-", class: ""},
                    {data: 'price', name: 'price', defaultContent: "-", class: ""},
                    {data: 'updated_at', name: 'updated_at', defaultContent: null}
                    ],
                    order: [
                        [6, 'desc']
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
                total4 = api.column(4).data().reduce(function(a, b) { return intVal(a) + intVal(b); }, 0);

                total5 = api.column(5).data().reduce(function(a, b) { return intVal(a) + intVal(b); }, 0);

                // Update footer
                $(api.column(4).footer()).html(total4.toLocaleString());
                $(api.column(5).footer()).html(total5.toLocaleString());


        }
            }); 
        });

        $(document).on('change', '.item', function() {
                 var booking_user_name = $('#booking_user_name').val();
                var item = $('.item').val();
                var trash = $('.trashswitch').prop('checked') ? 1 : 0;
                app_table.ajax.url(`{{url('/admin/remain_items?item=`+item+`&trash=`+trash+`/')}}`).load();

        });

</script>
@include('backend.admin.layouts.assets.trash_script')
@endsection
