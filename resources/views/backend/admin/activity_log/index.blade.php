@extends('backend.admin.layouts.app')

@section('meta_title', 'Activity Logs')
@section('page_title')
@lang("message.header.activity_log")
@endsection
@section('activity-log-active','mm-active')

@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection

@section('page_title_buttons')
    
@endsection

@section('content')

<div class="d-inline-block mb-2">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fas fa-calendar-alt mr-1"></i> @lang("message.header.activity_date") : </span>
            </div>
            <input type="text" class="form-control datepicker" placeholder="All">
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
                                <th>@lang("message.header.source")</th>
                                <th>@lang("message.header.causer")</th>
                                <th>@lang("message.description")</th>
                                <th>@lang("message.header.subject")</th>
                                <th> @lang("message.date") </th>
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
    $(function() {
            var route_model_name = "activity_log";
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
                    'url' : '{{ url("/admin/activity_log?trash=0") }}',
                    'type': 'GET',
                },
                columns: [{
                        data: "plus-icon",
                        name: "plus-icon",
                        defaultContent: null
                    },
                    {
                        data: 'source',
                        name: 'source',
                        defaultContent: "-",
                        class: ""
                    },
                    {
                        data: 'causer',
                        name: 'causer',
                        defaultContent: "-",
                        class: ""
                    },
                    {
                        data: 'description',
                        name: 'description',
                        defaultContent: "-",
                        class: ""
                    },
                    {       
                        data: 'subject',
                        name: 'subject',
                        defaultContent: "-",
                        class: ""
                    },

                    {
                        data: 'created_at',
                        name: 'created_at',
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
                    [3, 'desc']
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

    

        $(document).on('click', '.trash', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            swal("Are you sure, you want to Delete?", {
                    className: "danger-bg",
                    buttons: [true, "Yes"],
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: '/admin/activity_log/' + id ,
                            url :`{{url('/admin/activity_log/`+id+`/')}}`,
                            type: 'GET',
                            success: function () {
                                table.ajax.reload();
                            }
                        });
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
            table.ajax.url(`{{url('/admin/activity_log?daterange=`+daterange+`/')}}`).load();

        });

        $('.datepicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            var daterange = $('.datepicker').val();
            table.ajax.url(`{{url('/admin/activity_log?daterange=`+daterange+`/')}}`).load();

        }); 


    });
</script>
@endsection
