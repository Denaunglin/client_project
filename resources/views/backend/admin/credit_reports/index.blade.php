@extends('backend.admin.layouts.app')

@section('meta_title', 'Credit Reports')
@section('page_title', 'Credit Reports')
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

@can('add_item')
<a href="{{route('admin.credit_reports.create')}}" title="Add Credit Report" class="btn btn-primary action-btn">Add Credit Report</a>
@endcan
@endsection

@section('content')
<div class="pb-3">
    <div class="row">
      
        <div class="col-md-6 col-sm-12 col-xl-3">
                    <div class="d-inline-block mb-2 " style="width:100%">
                    <div class="input-group" >
                        <div class="input-group-prepend"><span class="input-group-text">Item Name : </span></div>
                        <select class="custom-select item mr-1" >
                            <option value="">All</option>
                            @forelse($item as $data)
                            <option value="{{$data->id}}">{{$data->name}}</option>
                            @empty
                            <option value="">There is no Item Data !</option>
                            @endforelse
                        </select>
                    </div>
                </div>
        </div>
        <div class="col-md-6 col-sm-12 col-xl-3">
                <div class="d-inline-block mb-2"style="width:100%">
                    <div class="input-group" >
                        <div class="input-group-prepend"><span class="input-group-text">Item Category : </span></div>
                        <select class="custom-select item_category mr-1">
                            <option value="">All</option>
                            @forelse($item_category as $data)
                            <option value="{{$data->id}}">{{$data->name}}</option>
                            @empty
                            <option value="">There is no Item Data !</option>
                            @endforelse
                        </select>
                    </div>
            </div>
        </div>
        <div class="col-md-6 col-sm-12 col-xl-3">
            <div class="d-inline-block mb-2"style="width:100%">
                <div class="input-group" >
                    <div class="input-group-prepend"><span class="input-group-text">Item Sub Category : </span></div>
                    <select class="custom-select item_sub_category mr-1">
                        <option value="">All</option>
                            @forelse($item_sub_category as $data)
                            <option value="{{$data->id}}">{{$data->name}}</option>
                            @empty
                            <option value="">There is no Item Data !</option>
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
                                <th >Item </th>
                                <th> Customer</th>
                                <th> Original Amount</th>
                                <th> Credit Amount</th>
                                <th>  Remain Amount  <br></th>
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
    var route_model_name = "order_lists";
        var app_table;
        $(function() {
            app_table = $('.data-table').DataTable({
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
                ajax: `${PREFIX_URL}/admin/${route_model_name}?trash=0`,
                columns: [
                    {data: 'plus-icon', name: 'plus-icon', defaultContent: "-", class: ""},
                    {data: 'item', name: 'item', defaultContent: "-", class: ""},
                    {data: 'customer', name: 'customer', defaultContent: "-", class: ""},
                    {data: 'original_amount', name: 'original_amount', defaultContent: "-", class: ""},
                    {data: 'credit_amount', name: 'credit_amount', defaultContent: "-", class: ""},
                    {data: 'paid_amount', name: 'paid_amount', defaultContent: "-", class: ""},
                    {data: 'remain_amount', name: 'remain_amount', defaultContent: "-", class: ""},
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


        $(document).on('change', '.item, .item_category , .item_sub_category', function() {
                 var booking_user_name = $('#booking_user_name').val();
                var daterange = $('.datepicker').val();
                var item = $('.item').val();
                var item_category = $('.item_category').val();
                var item_sub_category=$('.item_sub_category').val();
                var trash = $('.trashswitch').prop('checked') ? 1 : 0;
                app_table.ajax.url(`${PREFIX_URL}/admin/${route_model_name}?item=${item}&item_category=${item_category}&item_sub_category=${item_sub_category}&trash=${trash}`).load();
        });

</script>
@include('backend.admin.layouts.assets.trash_script')
@endsection
