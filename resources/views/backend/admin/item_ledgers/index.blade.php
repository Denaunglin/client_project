@extends('backend.admin.layouts.app')

@section('meta_title', 'Items Ledgers')
@section('page_title', 'Items Ledgers')
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
<a href="{{route('admin.item_ledgers.create')}}" title="Add Item Ledgers" class="btn btn-primary action-btn">Add ItemLedgers</a>
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
                            <tr >
                                <th rowspan="2">Barcode</th>
                                <th rowspan="2">Item Name</th>
                                <th rowspan="2">Unit</th>
                                <th rowspan="2">Sub Group</th>
                                <th rowspan="2">Opening Qty</th>
                                <th colspan="2" class="text-center bg-light">Byuing List</th>
                                <th colspan="2" class="text-center bg-light">Selling List</th>
                                <th colspan="2" class="text-center bg-light">Transform</th> 
                                <th rowspan="2" class="text-center ">Adjustment </th>
                                <th rowspan="2" class="text-center ">Closing Qty </th>
                                <th rowspan="2">Updated at</th>
                                <th rowspan="2" class="no-sort action">Action</th>    
                            </tr>   
                            <tr >
                                <th class="text-center">Shop Qty</th>
                                <th class="text-center">Return Qty</th>
                                 <th class="text-center">Shop Qty</th>
                                <th class="text-center">Return Qty</th>
                                <th class="text-center">Input Qty</th>
                                <th class="text-center">Output Qty</th>  
                            </tr>
                        </thead>
                        <tbody></tbody>
                        {{-- <tfoot class="bg-light">
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot> --}}
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    var route_model_name = "item_ledgers";
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
                    {data: 'barcode', name: 'barcode', defaultContent: "-", class: ""},
                    {data: 'item_id', name: 'item_id', defaultContent: "-", class: ""},
                    {data: 'unit', name: 'unit', defaultContent: "-", class: ""},
                    {data: 'item_sub_category', name: 'item_sub_category', defaultContent: "-", class: ""},
                    {data: 'opening_qty', name: 'opening_qty', defaultContent: "-", class: ""},
                    {data: 'buying_buy', name: 'buying_buy', defaultContent: "-", class: ""},
                    {data: 'buying_back', name: 'buying_back', defaultContent: "-", class: ""},
                    {data: 'selling_sell', name: 'selling_sell', defaultContent: "-", class: ""},
                    {data: 'selling_back', name: 'selling_back', defaultContent: "-", class: ""},
                    {data: 'adjust_in', name: 'adjust_in', defaultContent: "-", class: ""},
                    {data: 'adjust_out', name: 'adjust_out', defaultContent: "-", class: ""},
                    {data: 'adjust_list', name: 'adjust_list', defaultContent: "-", class: ""},
                    {data: 'closing_qty', name: 'closing_qty', defaultContent: "-", class: ""},
                    {data: 'action', name: 'action', orderable: false, searchable: false, class: "action"},
                    {data: 'updated_at', name: 'updated_at', defaultContent: null}
                    ],
                    order: [
                        [12, 'desc']
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
