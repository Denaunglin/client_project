@extends('backend.admin.layouts.app')

@section('meta_title', 'Opening Items')
@section('page_title')
@lang("message.header.opening_item")
@endsection
@section('opening-item-active','mm-active')

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
    <a href="{{route('admin.opening_items.create')}}" title="Add Category" class="btn btn-primary action-btn">@lang("message.header.add_opening_item")</a>
    @endcan
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
                            <option value="">@lang("message.header.select_item")</option>
                            @forelse($item as $data)
                            <option value="{{$data->id}}">{{$data->name}}</option>
                            @empty
                            <option value="">@lang("message.header.there_is_no_data")!</option>
                            @endforelse
                        </select>
                    </div>
                </div>
        </div>
        <div class="col-md-6 col-sm-12 col-xl-3">
            <div class="d-inline-block mb-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar-alt mr-1"></i> @lang("message.header.created_at") : </span>
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
                                <th>@lang("message.header.barcode")</th>
                                <th>@lang("message.header.item_name")</th>
                                <th>@lang("message.header.unit")</th>
                                <th>@lang("message.header.item_category")<br></th>
                                <th>@lang("message.header.item_sub_category")</th>
                                <th>@lang("message.header.qty")</th>
                                <th>@lang("message.header.rate_per_unit")</th>
                                <th>@lang("message.header.discount")</th>
                                <th>@lang("message.header.total_price")</th>
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
<script>
    var route_model_name = "opening_items";
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
              filename: 'Opening Item Data Report',
              orientation: 'portrait', //portrait
              pageSize: 'A4', //A3 , A5 , A6 , legal , letter
              exportOptions: {
                  columns: [2,3,4,5,6,7,8,9]
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
                                  text: 'Opening Item Data Report',
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
                    'url' :'{{ url("/admin/opening_items?trash=0") }}',
                    'type': 'GET',
                },
                columns: [
                    {data: 'plus-icon', name: 'plus-icon', defaultContent: "-", class: ""},
                    {data: 'barcode', name: 'barcode', defaultContent: "-", class: ""},
                    {data: 'item_id', name: 'name', defaultContent: "-", class: ""},
                    {data: 'unit', name: 'unit', defaultContent: "-", class: ""},
                    {data: 'item_category', name: 'item_category', defaultContent: "-", class: ""},
                    {data: 'item_sub_category', name: 'item_sub_category', defaultContent: "-", class: ""},
                    {data: 'qty', name: 'qty', defaultContent: "-", class: ""},
                    {data: 'price', name: 'price', defaultContent: "-", class: ""},
                    {data: 'discount', name: 'discount', defaultContent: "-", class: ""},
                    {data: 'net_price', name: 'net_price', defaultContent: "-", class: ""}, 
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
            app_table.ajax.url(`{{url('/admin/opening_items?daterange=`+daterange+`&trash=`+trash+`/')}}`).load();

        });

        $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');

            var daterange = $('.datepicker').val();
            var trash = $('.trashswitch').prop('checked') ? 1 : 0;
            app_table.ajax.url(`{{url('/admin/opening_items?daterange=`+daterange+`&trash=`+trash+`/')}}`).load();
        }); 

        $(document).on('change', '.item', function() {
                var daterange = $('.datepicker').val();
                var item = $('.item').val();
                var item_category = $('.item_category').val();
                var item_sub_category=$('.item_sub_category').val();
                var trash = $('.trashswitch').prop('checked') ? 1 : 0;
                app_table.ajax.url(`{{url('/admin/opening_items?item=`+item+`&trash=`+trash+`&item_sub_category=`+item_sub_category+`&item_category=`+item_category+`/')}}`).load();
        });

        $(document).on('change', '.trashswitch', function () {
            if ($(this).prop('checked') == true) {
                var trash = 1;
            } else {
                var trash = 0;
            }
            app_table.ajax.url(`{{url('/admin/opening_items?trash=`+trash+`/')}}`).load();
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
                            url :`{{url('/admin/opening_items/`+id+`/trash')}}`,

                            type: 'GET',
                            success: function () {
                                app_table.ajax.reload();
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
                            url :`{{url('/admin/opening_items/`+id+`/restore')}}`,
                            type: 'GET',
                            success: function () {
                                app_table.ajax.reload();
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
                            url :`{{url('/admin/opening_items/`+id+`/')}}`,
                            type: 'GET',
                            success: function () {
                                app_table.ajax.reload();
                            }
                        });
                    }
                });
        });

</script>
@include('backend.admin.layouts.assets.trash_script')
@endsection
