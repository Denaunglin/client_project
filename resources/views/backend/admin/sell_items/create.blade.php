@extends('backend.admin.layouts.app')
@section('meta_title', 'Add Commodity Sales Item')
@section('page_title')
@lang("message.header.add_selling_item")
@endsection
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.sell_items.store') }}" method="post" id="create" enctype="multipart/form-data">
                    @csrf
                        <div class="container">
                            <div class="print-data">

                            <div class="row clearfix">
                                <div class="col-md-4 mb-3">
                                    <label for="">@lang("message.header.customer")</label>
                                    <select name="customer_id" class="form-control" id="">
                                        <option value="0">Default Customer</option>
                                        @foreach($customer as $data) 
                                    <option value="{{$data->id}}">{{$data->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <button class="btn btn-primary float-right mt-3 print">@lang("message.header.print_slip")</button>
                                </div>
                                <div class="col-md-12 column">
                                    <table class="table table-bordered table-hover" id="tab_logic">
                                        <thead>
                                            <tr >
                                                <th class="text-center">
                                                    @lang("message.header.id")
                                                </th>
                                                <th class="text-center">
                                                    @lang("message.header.item")
                                                </th>
                                                <th class="text-center">
                                                    @lang("message.header.qty")
                                                </th>
                                                <th class="text-center">
                                                    @lang("message.header.rate_per_unit")
                                                </th>
                                                <th class="text-center">
                                                    @lang("message.header.discount")
                                                </th>
                                                <th class="text-center">
                                                    @lang("message.header.total_price")
                                                </th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id='addr0'>
                                                <td>
                                                1
                                                </td>
                                                <td>
                                                    <div class="row">
                                                    
                                                        <div class="col-md-12 mb-3">
                                                            <input type="search" id="search" autocomplete="off" name="search"  placeholder="search" class="form-control">
                                                        </div>
            
                                                        <div class="col-12">
                                                            <select class="form-control custom-select" id="item_id" name="item_id[]"  required>
                                                                <option value="">@lang("message.header.choose_item")</option>
                                                                @forelse($item as $data)
                                                                <option value="{{$data->id}}">{{$data->name }}</option>
                                                                @empty<p>@lang("message.header.there_is_no_data")</p>
                                                                @endforelse
                                                            </select>   
                                                           
                                                        </div>
                                                    </div>                                        
                                                </td>
                                                <td>
                                                    <input type="number" id="numeric_value" name="qty" class="form-control  @error('qty') is-invalid @enderror" placeholder='Qty' >
                                                </td>
                                                <td>
                                                    <input type="number" id="aa" name="price" class="form-control  @error('price') is-invalid @enderror" placeholder='Rate Per Unit' >
                                                </td>
                                                <td>
                                                    <input type="number" id="discount" name="discount" class="form-control  @error('discount') is-invalid @enderror" placeholder='Discount' >
                                                </td>
                                                <td>
                                                    <input type="number" id="net_price" name="net_price" class="form-control  @error('net_price') is-invalid @enderror" placeholder="Total Price" >
                                                </td>
                                            </td>
                                            </tr>
                                            <tr id='addr1'></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <a id="add_row" class="btn btn-default pull-left">Add Row</a><a id='delete_row' class="pull-right btn btn-default">Delete Row</a>
                        </div>  
                        </div>
                    <div class="row my-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.sell_items.index') }}" class="btn btn-danger mr-3">@lang("message.cancel")</a>
                            <input type="submit" value="@lang("message.confirm")" class="btn btn-success">
                        </div>
                       
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

{!! JsValidator::formRequest('App\Http\Requests\ItemRequest','#create') !!}
<script>
     $(document).ready(function(){
      var items = {!! json_encode($item) !!};
      var i=1;
      var text = "";
    for (var l=0 ; l < items.length; l++) {
    text +=  '<option value='+items[l].id+'>'+items[l].name+'</option>';
    } 

     $("#add_row").click(function(){
        $('#addr'+i).html("<td>"+ (i+1) +"</td><td><div class='row'><div class='col-md-12 mb-3'><input type='search' class='form-control' id='search"+i+"' autocomplete='off' name='search' placeholder='search' ></div><div class='col-md-12'><select class='custom-select' id='item_id"+i+"' name='item_id[]"+i+"' required><option value=''>Choose Item Category</option>"+text+"</select></td></div></div><td><input  name='qty"+i+"' type='number' id='numeric_value"+i+"' autofocus='autofocus' placeholder='Qty'  class='form-control input-md'></td><td><input  id='aa"+i+"' name='price"+i+"' autofocus='autofocus' type='number' placeholder='Rate Per Unit'  class='form-control numeric_value"+i+"  input-md'></td><td><input  id='discount"+i+"' name='discount"+i+"' autofocus='autofocus' type='number' placeholder='Discount'  class='form-control discount"+i+"  input-md'></td><td><input  name='net_price' type='number' placeholder='Total Price' id='net_price"+i+"' class='form-control  input-md'></td>");
      $('#tab_logic').append('<tr id="addr'+(i+1)+'"></tr>');

    var a = i;
    $('#discount'+a).keyup(function() {
    var price = $("#aa"+a).val();
    var qty = $("#numeric_value"+a).val();
    var sum = 0;
    sum = (qty * price ) - Number($(this).val()) ;
    $('#net_price'+a).val(sum);
    });

    $('#search'+a).change(function(e) {
            let search = $(this).val();
            $.get('/get_item?search=' + search, function(data) {
                    $('#item_id'+a).empty();
        $('#item_id'+a).append('<option disabled selected>'+ 'Choose Item' + '</option>');
                    $.each(data, function( key, value ) {
                      $('#item_id'+a).append('<option value="'+value.id+'" >'+value.name+'</option>');
            });
        });
    });

    $('#item_id'+a).on('change', function(e) {
        let item = $(this).val();
        $.get('/get_item?item=' + item, function(data) {
                $('#aa'+a).empty();
        $('#aa'+a).val(data.buying_price);
    });
});

      i++; 
  });

     $("#delete_row").click(function(){
         if(i>1){
         $("#addr"+(i-1)).html('');
         i--;
         }
     });

});

$('#discount').keyup(function() {
    var price = $("#aa").val();
    var qty = $("#numeric_value").val();
    var sum = 0;
    sum = (qty * price ) - Number($(this).val()) ;
    
    $('#net_price').val(sum);
    }); 

    $('#search').change(function(e) {
            let search = $(this).val();
            $.get('/get_item?search=' + search, function(data) {
                    $('#item_id').empty();
        $('#item_id').append('<option disabled selected>'+ 'Choose Item' + '</option>');
                    $.each(data, function( key, value ) {
                      $('#item_id').append('<option value="'+value.id+'" >'+value.name+'</option>');
        });
    });
});

$('#item_id').on('change', function(e) {
            let item = $(this).val();
            $.get('/get_item?item=' + item, function(data) {
                    $('#aa').empty();
            $('#aa').val(data.buying_price);
        });
    });



// $('#item_id').change(function(e){
//             var item_id =parseInt($('#item_id').val());
//             var items = {!! json_encode($item) !!};
//             var text ="";
//             for (var l=0 ; l < items.length; l++) {
//                 if(items[l] == item_id)
//             text +=  '<option value='+items[l].id+'>'+items[l].name+'</option>';
//             } 

//             $i=0;
//             if(extra_bed_qty !=0){
//             $('#price').empty();
//             var check = (room_qty ) * (parseInt(extra_bed_qty));
//             for(i=0;i <= check;i++){
//                 $('#extra_bed_qty').append(`<option value='${i}'>${i}</option>`);
//                     }
//             }
//         });

//     });

$(document).on('click', '.print', function(e) {
                e.preventDefault();
                var divContents = $(`.print-data`).html();
                var printWindow = window.open('', '', 'height=400,width=800');
                printWindow.document.write(`<html><head>
                <style>
                        @font-face {
                            font-family: 'Unicode';
                            src: url(/fonts/TharLonUni.ttf);
                        }
                        * {
                            font-family: 'Unicode';
                        }
                        @page {
                            margin: 0.5cm 0.5cm !important;
                        }
                        * {
                            -webkit-print-color-adjust: exact;
                        }
                        body {
                            margin: 5px 1rem !important;
                        }

                        .apex {
                           backgroud-color:blue;
                            color: red;
                        }

                        .text-left{
                            text-align: left !important;
                        }

                        table {
                            margin-top:100px !important;
                            width: 100%;
                            margin-bottom: 1rem;
                            background-color: #3333;
                        }

                        tbody tr td{
                            font-size:18px !important;
                        }
                        tbody tr{
                            margin-bottom:20px !important;
                            margin-top:20px !important;
                        }

                        .text-center {
                            text-align: center !important;
                        }


                        .table thead th {
                            vertical-align: bottom;
                            border-bottom: 2px solid #33333;
                            border-bottom-width: 2px;
                        }

                      
                        .table-bordered th, .table-bordered td {
                            border: 1px solid #e9ecef;
                        }

                        .table td {
                            max-width: 150px !important;
                            font-size: 10px;
                            backgroud-color:blue;
                            vertical-align: bottom;
                            border-bottom: 2px solid #33333;
                            border-bottom-width: 2px;
                        }
                       
                        .badge {
                        font-weight: bold;
                        text-transform: uppercase;
                        padding: 5px 10px;
                        min-width: 19px;
                        margin-top:10px;
                        }

                        .room_close{
                            color:red;
                        }

                        a{
                            text-decoration:none;
                            color:black;
                        }
                        
                        .deluxe {
                            background-color: #DA9694;
                        }

                        .e_suite {
                            background-color: #FAFF02;
                        }

                        .app-main .app-main__inner {
                            background-color: #f1f4f6;
                        }

                        .standard {
                            background-color: #F79646;
                        }

                        .superior {
                            background-color: #28a745;
                        }

                        .separate {
                            background-color: gray;
                        }

                        .suite {
                            background-color: #ABBD87;
                        }

                        .ambassador {
                            background-color: #87ceeb;
                        }

                        .vrt {
                            border :none !important;
                            transform: rotate(270deg);
                        }

                        .room-plan-table th,
                        .room-plan-table td {
                            max-width: 100px !important;
                            font-size: 10px;
                        }

                        .room-plan-table td p {
                            font-weight: bold;
                            color: #000;
                        }

                        .ambassador td p {
                            color: #87ceeb;
                        }

                        .badge-danger{
                         color:white;
                         padding:3px;
                         font-size:10px;
                         background-color:red;
                        }

                        .badge-primary{
                            margin-top:10px;
                            color:white;
                            padding:3px;
                            font-size:10px;
                            background-color:blue
                        }

                        .badge-warning{
                            margin-top:10px;
                            color:black;
                             font-size:9px;
                            padding:3px;
                            background-color:#f7b924
                        }
                      
                        .standard {
                            background-color: #F79646;
                        }

                        .superior {
                            background-color: #28a745;
                        }

                        .separate {
                            background-color: gray;
                        }

                        .suite {
                            background-color: #ABBD87;
                        }
                            
                        .pricing-data,
                        .border,
                        .row {
                            display: flex !important;
                            flex-wrap: wrap !important;
                            margin-right: -15px !important;
                            margin-left: -15px !important;
                        }
                        .col-md-12 {
                            width: 100vw !important;
                            padding: 5px !important;
                        }
                        .col-12 {
                            flex: 0 0 100% !important;
                            max-width: 100% !important;
                        }
                        .col-6 {
                            flex: 0 0 50% !important;
                            max-width: 50% !important;
                        }
                        .my-0 {
                            margin: 0 !important;
                        }
                        .mb-0 {
                            margin-bottom: 0 !important;
                        }
                        .mb-2 {
                            margin: .5rem !important
                        }
                        .mb-4 {
                            margin-bottom: 1.5rem !important;
                        }
                        .text-muted {
                            color: #6c757d !important;
                        }
                        .p-1 {
                            padding: 1 !important;
                        }
                        .text-center {
                            text-align: center !important;
                        }
                        .bg-light {
                            background-color: #eee !important;
                        }
                    
                        h5{
                            font-size:20px !important;
                            margin-bottom:15px !important;
                        }
                        p{
                            font-size:14px !important;
                        }
                        </style>`
                        
                        );

                printWindow.document.write('</head><body>');
                printWindow.document.write(divContents);
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                setTimeout(function() {
                    printWindow.print();
                }, 500);
            });
</script>
@endsection
