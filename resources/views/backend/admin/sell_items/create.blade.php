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
                                                    <select class="form-control custom-select" id="item_id" name="item_id[]" required>
                                                        <option value="">Choose Item Category</option>
                                                        @forelse($item as $data)
                                                        <option value="{{$data->id}}">{{$data->name }}</option>
                                                        @empty<p>There is no data</p>
                                                        @endforelse
                                                    </select>                                             
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
      $('#addr'+i).html("<td>"+ (i+1) +"</td><td><select class='custom-select' name='item_id[]"+i+"' required><option value=''>Choose Item Category</option>"+text+"</select></td><td><input id='numeric_value"+i+"'  name='qty"+i+"' type='number' placeholder='Qty'  class='form-control input-md'></td><td><input id='aa"+i+"'   name='price"+i+"' type='number' placeholder='Rate Per Unit'  class='form-control input-md'></td><td><input id='discount"+i+"'  name='discount"+i+"' type='number' placeholder='Discount'  class='form-control input-md'></td><td><input id='net_price"+i+"'   name='net_price"+i+"' type='number' placeholder='Total Price'  class='form-control input-md'></td>");
      $('#tab_logic').append('<tr id="addr'+(i+1)+'"></tr>');

    var a = i;
    $('#discount'+a).keyup(function() {
    var price = $("#aa"+a).val();
    var qty = $("#numeric_value"+a).val();
    var sum = 0;
    sum = (qty * price ) - Number($(this).val()) ;
    $('#net_price'+a).val(sum);
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
    console.log(qty);
    var sum = 0;
    sum = (qty * price ) - Number($(this).val()) ;
    
    $('#net_price').val(sum);
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
</script>
@endsection
