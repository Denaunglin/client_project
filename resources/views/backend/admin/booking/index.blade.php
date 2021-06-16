@extends('backend.admin.layouts.app')
@section('meta_title', 'Booking')
@section('page_title', 'Booking')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('page_title_buttons')
<div class="d-flex justify-content-end">
    <div class="custom-control custom-switch p-2 mr-3">
        <input type="checkbox" class="custom-control-input trashswitch" id="trashswitch">
        <label class="custom-control-label" for="trashswitch"><strong>Trash</strong></label>
    </div>

    @can('add_booking')
    <a href="{{route('admin.booking.create')}}" title="Add Booking" class="btn btn-primary action-btn">Add Booking</a>
    @endcan

</div>
@endsection

@section('content')
<div class="pb-3">
<div class="row">
    <div class="col-md-6 col-sm-12 col-xl-3">
        <div class="d-inline-block mb-2" style="width:100%">
                <div class="input-group" >
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-calendar-alt mr-1"></i> Booking Date : </span>
                    </div>
                    <input type="text" class="form-control datepicker" placeholder="All">
                </div>
         </div>
    </div>
    <div class="col-md-6 col-sm-12 col-xl-3">
        <div class="d-inline-block mb-2" style="width:100%">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user mr-1"></i> User Name : </span>
                    </div>
                    <select class="custom-select username mr-1">
                        <option value="">All</option>
                        @foreach($username as $data)
                        <option value="{{$data->name}}">{{$data->name}}</option>
                        @endforeach
                    </select>
                </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12 col-xl-3">
                <div class="d-inline-block mb-2 " style="width:100%">
                <div class="input-group" >
                    <div class="input-group-prepend"><span class="input-group-text">Status : </span></div>
                    <select class="custom-select status mr-1" >
                        <option value="">All</option>
                        {!!App\Helper\HelperFunction::statusOptions()!!}    
                    </select>
                </div>
            </div>
    </div>
    <div class="col-md-6 col-sm-12 col-xl-3">
            <div class="d-inline-block mb-2"style="width:100%">
                <div class="input-group" >
                    <div class="input-group-prepend"><span class="input-group-text">Payment Status : </span></div>
                    <select class="custom-select payment_status mr-1">
                        <option value="">All</option>
                        {!!App\Helper\HelperFunction::paymentStatusOptions()!!}
                    </select>
                </div>
        </div>
    </div>
    <div class="col-md-12 col-sm-12 col-xl-12">
            <div class="d-inline-block mb-2"style="width:100%">
                <div class="input-group" >
                    <div class="input-group-prepend"><span class="input-group-text">User Search: </span></div>
                    <input type="text" id="booking_user_name" name="booking_user_name"><button type="button" id="button" class="btn btn-primary"><span class="fa fa-search"></span></button>
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
                    <table class="align-middle m-0 table table-bordered data-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Booking No.</th>
                                <th>Booking Date</th>
                                <th>Checkin - Checkout</th>
                                <th>Room</th>
                                <th>Person</th>
                                <th>Status</th>
                                <th>Client <br> Cancel</th>
                                <th class="no-sort action">Action</th>
                                <th class="d-none hidden">Updated at</th>
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
    var route_model_name = "booking";
        var app_table;
        $(function() {
            app_table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                dom: 'Bfrtip',
                buttons: [
                    {
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    extend: 'pdfHtml5',
                    filename: 'Booking Report',
                    orientation: 'portrait', //portrait
                    pageSize: 'A4', //A3 , A5 , A6 , legal , letter
                    exportOptions: {
                        columns: [1,2,3,4,5,6,7]
                    },
                    customize: function(doc) {
                        //Remove the title
                        doc.content.splice(0, 1);
                        var report_time = moment().format('YYYY-MM-DD HH:mm:ss');
                        doc.pageMargins = [20, 60, 20, 30];
                        doc.defaultStyle.fontSize = 6;
                        doc.defaultStyle.font = 'NotoSansMyanmar';
                        doc.styles.tableHeader.fontSize = 8;
                        doc.content[0].table.widths = '*';

                        // Header
                        doc['header'] = (function() {
                            return {
                                columns: [{
                                        alignment: 'left',
                                        italics: true,
                                        text: 'Booking Report',
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
                lengthMenu: [
                    [10, 25, 50, 100, 500],
                    ['10 rows', '25 rows', '50 rows', '100 rows', '500 rows']
                ],
                ajax: `${PREFIX_URL}/admin/${route_model_name}?trash=0`,
                columns: [

                    {
                        data: "plus-icon",
                        name: "plus-icon",
                        defaultContent: null
                    },
                    {data: 'booking_number', name: 'booking_number', defaultContent: "-", class: ""},
                    {data: 'created_at', name: 'created_at',defaultContent: "-", class: ""},
                    {data: 'checkin_checkout', name: 'checkin_checkout', defaultContent: "-", class: ""},
                    {data: 'room', name: 'room', defaultContent: "-", class: ""},
                    {data: 'person', name: 'person', defaultContent: "-", class: ""},
                    {data: 'status', name: 'status', defaultContent: "-", class: ""},
                    {data: 'cancellation', name: 'cancellation', defaultContent: "-", class: ""},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                    {data: 'updated_at', name: 'updated_at', defaultContent: null}
                    ],
                    order: [
                        [8, 'desc']
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
                var booking_user_name = $('#booking_user_name').val();
                var daterange = $('.datepicker').val();
                var status = $('.status').val();
                var payment_status = $('.payment_status').val();
                var username=$('.username').val();
                var trash = $('.trashswitch').prop('checked') ? 1 : 0;
                app_table.ajax.url(`${PREFIX_URL}/admin/${route_model_name}?daterange=${daterange}&status=${status}&payment_status=${payment_status}&booking_user_name=${booking_user_name}&trash=${trash}`).load();
            });

            $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');

                var booking_user_name = $('#booking_user_name').val();
                var daterange = $('.datepicker').val();
                var status = $('.status').val();
                var payment_status = $('.payment_status').val();
                var username=$('.username').val();
                var trash = $('.trashswitch').prop('checked') ? 1 : 0;
                app_table.ajax.url(`${PREFIX_URL}/admin/${route_model_name}?daterange=${daterange}&status=${status}&payment_status=${payment_status}&booking_user_name=${booking_user_name}&trash=${trash}`).load();
            });

            $(document).on('change', '.status, .payment_status', function() {
                 var booking_user_name = $('#booking_user_name').val();
                var daterange = $('.datepicker').val();
                var status = $('.status').val();
                var payment_status = $('.payment_status').val();
                var username=$('.username').val();
                var trash = $('.trashswitch').prop('checked') ? 1 : 0;
                app_table.ajax.url(`${PREFIX_URL}/admin/${route_model_name}?daterange=${daterange}&status=${status}&payment_status=${payment_status}&booking_user_name=${booking_user_name}&trash=${trash}`).load();
            });


            $(document).on('change', '.username', function() {
                 var booking_user_name = $('#booking_user_name').val();
                var daterange = $('.datepicker').val();
                var status = $('.status').val();
                var payment_status = $('.payment_status').val();
                var username=$('.username').val();
                var trash = $('.trashswitch').prop('checked') ? 1 : 0;
                app_table.ajax.url(`${PREFIX_URL}/admin/${route_model_name}?daterange=${daterange}&status=${status}&payment_status=${payment_status}&booking_user_name=${booking_user_name}&username=${username}&trash=${trash}`).load();
            });

            $(document).on('click','#button',function(){
                var booking_user_name = $('#booking_user_name').val();
                var daterange = $('.datepicker').val();
                var status = $('.status').val();
                var payment_status = $('.payment_status').val();
                var username=$('.username').val();
                var trash = $('.trashswitch').prop('checked') ? 1 : 0;
                app_table.ajax.url(`${PREFIX_URL}/admin/${route_model_name}?daterange=${daterange}&status=${status}&payment_status=${payment_status}&booking_user_name=${booking_user_name}&username=${username}&trash=${trash}`).load();
          
            });
        });
        
</script>
@include('backend.admin.layouts.assets.trash_script')
@endsection
