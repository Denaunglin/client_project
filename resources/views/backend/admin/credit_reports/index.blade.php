@extends('backend.admin.layouts.app')

@section('meta_title', 'Credit Reports')
@section('page_title')
@lang("message.header.credit_report")
@endsection
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('credit-active','mm-active')


@section('page_title_buttons')
<div class="d-flex justify-content-end">
    <div class="custom-control custom-switch p-2 mr-3">
        <input type="checkbox" class="custom-control-input trashswitch" id="trashswitch">
        <label class="custom-control-label" for="trashswitch"><strong>@lang("message.header.trash")</strong></label>
    </div>
</div>

@can('add_item')
<a href="{{route('admin.credit_reports.create')}}" title="Add Credit Report" class="btn btn-primary action-btn">@lang("message.header.add_credit_report")</a>
@endcan
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
       
        <div class="col-md-6 col-sm-12 col-xl-3">
            <div class="d-inline-block mb-2 " style="width:100%">
                <div class="input-group" >
                    <div class="input-group-prepend"><span class="input-group-text">@lang("message.header.item_name") : </span></div>
                    <select class="custom-select item mr-1" >
                        <option value="">@lang("message.header.all")</option>
                        @forelse($item as $data)
                        <option value="{{$data->id}}">{{$data->name}}</option>
                        @empty
                        <option value="">@lang("message.header.there_is_no_data")</option>
                        @endforelse
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-xl-3">
                <div class="d-inline-block mb-2"style="width:100%">
                    <div class="input-group" >
                        <div class="input-group-prepend"><span class="input-group-text"> @lang("message.header.customer") : </span></div>
                        <select class="custom-select customer mr-1">
                            <option value="">@lang("message.header.all")</option>
                            @forelse($customer as $data)
                            <option value="{{$data->id}}">{{$data->name}}</option>
                            @empty
                            <option value="">@lang("message.header.there_is_no_data")</option>
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
                                <th>@lang("message.header.item")</th>
                                <th>@lang("message.header.customer")</th>
                                <th>@lang("message.header.original_amount")</th>
                                <th>@lang("message.header.paid_amount")</th>
                                <th>@lang("message.header.credit_amount")</th>
                                <th>@lang("message.header.paid_date")</th>
                                <th>@lang("message.header.paid_times")</th>
                                <th>Paid Status</th>
                                <th>@lang("message.header.action")</th>
                                <th>@lang("message.header.updated_at")</th>
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
{!! JsValidator::formRequest('App\Http\Requests\CreditRequest', '#create') !!}
<script>
    var route_model_name = "credit_reports";
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
              filename: 'Credit Report',
              orientation: 'portrait', //portrait
              pageSize: 'A4', //A3 , A5 , A6 , legal , letter
              exportOptions: {
                  columns: [1,2,3,4,5,6,7,8,9]
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
                                  text: 'Credit Report',
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
                        'url' : '{{ url("/admin/credit_reports?trash=0") }}',
                        'type': 'GET',
                    },
                columns: [
                    {data: 'plus-icon', name: 'plus-icon', defaultContent: "-", class: ""},
                    {data: 'item', name: 'item', defaultContent: "-", class: ""},
                    {data: 'customer', name: 'customer', defaultContent: "-", class: ""},
                    {data: 'origin_amount', name: 'origin_amount', defaultContent: "-", class: ""},
                    {data: 'paid_amount', name: 'paid_amount', defaultContent: "-", class: ""},
                    {data: 'credit_amount', name: 'credit_amount', defaultContent: "-", class: ""},
                    {data: 'paid_date', name: 'paid_date', defaultContent: "-", class: ""},
                    {data: 'paid_times', name: 'paid_times', defaultContent: "-", class: ""},
                    {data: 'paid_status', name: 'paid_status', defaultContent: "-", class: ""},
                    {data: 'action', name: 'action', orderable: false, searchable: false, class: "action"},
                    {data: 'updated_at', name: 'updated_at', defaultContent: null}
                    ],
                    order: [
                        [7, 'desc']
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
            app_table.ajax.url(`{{url('/admin/credit_reports?daterange=`+daterange+`&trash=`+trash+`/')}}`).load();
        });

        $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');

            var daterange = $('.datepicker').val();
            var trash = $('.trashswitch').prop('checked') ? 1 : 0;
            app_table.ajax.url(`{{url('/admin/credit_reports?daterange=`+daterange+`&trash=`+trash+`/')}}`).load();
        }); 

        $(document).on('change', '.trashswitch', function () {
            if ($(this).prop('checked') == true) {
                var trash = 1;
            } else {
                var trash = 0;
            }
            app_table.ajax.url('/admin/cash_books?trash=' + trash).load();
        });

        $(document).on('change', '.item, .customer ', function() {
                var booking_user_name = $('#booking_user_name').val();
                var daterange = $('.datepicker').val();
                var item = $('.item').val();
                var customer=$('.customer').val();
                var trash = $('.trashswitch').prop('checked') ? 1 : 0;
                app_table.ajax.url(`{{url('/admin/credit_reports?item=`+item+`&customer=`+customer+`&trash=`+trash+`/')}}`).load();
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
                            url :`{{url('/admin/credit_reports/`+id+`/trash')}}`,
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
                            url :`{{url('/admin/credit_reports/`+id+`/restore')}}`,
                            type: 'GET',
                            success: function () {
                                app_table.ajax.reload();
                            }
                        });
                    }
                });
        });

        // $(document).on('click', '.destroy', function (e) {
        //     e.preventDefault();
        //     var id = $(this).data('id');
        //     swal("Are you sure, you want to delete?", {
        //             className: "danger-bg",
        //             buttons: [true, "Yes"],
        //         })
        //         .then((willDelete) => {
        //             if (willDelete) {
        //                 $.ajax({
        //                     url :`{{url('/admin/credit_reports/`+id+`/delete')}}`,
        //                     type: 'GET',
        //                     success: function () {
        //                         app_table.ajax.reload();
        //                     }
        //                 });
        //             }
        //         });
        // });
        
</script>
@include('backend.admin.layouts.assets.trash_script')
@endsection
