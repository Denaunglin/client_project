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
    <div class="col-md-12 mb-3">
      
    </div>
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
                                    <select name="customer_id" class="form-control" id="customer_id">
                                        <option value="0">Default Customer</option>
                                        @foreach($customer as $data) 
                                    <option value="{{$data->id}}">{{$data->name}}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="sell_type" value="1">

                                </div>  
                                <div class="col-md-8">
                                    @can('add_item')
                                    @endcan
                                    <div class="modal fade mt-5" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                          <div class="modal-content">
                                            <div class="modal-header">
                                              <h5 class="modal-title" id="exampleModalLabel"> Pay
                                            </h5>
                                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                              </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{ route('admin.sell_items.store') }}" method="post" id="create" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="">Total Qty</label>
                                                                <input type="number" id="total_qty" name="total_qty"  class="form-control @error('qty') is-invalid @enderror">
                                                                @error('qty')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>@lang("message.header.discount") </label>
                                                                <input type="number" id="discount" value="0" name="discount" class="form-control  @error('discount') is-invalid @enderror" >
                                                                @error('discount')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>  
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Total Amount </label>
                                                                <input type="number" id="total_grand" name="origin_amount" class="form-control  @error('origin_amount') is-invalid @enderror" >
                                                                @error('origin_amount')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>@lang("message.header.paid_amount")</label>
                                                                <input type="number" id="paid_amount" value="0" name="paid_amount" class="form-control  @error('paid_amount') is-invalid @enderror" >
                                                                @error('paid_amount')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>@lang("message.header.credit_amount") </label>
                                                                <input type="number" id="credit_amount" value="0" name="credit_amount" class="form-control  @error('credit_amount') is-invalid @enderror" >
                                                                @error('credit_amount')
                                                                <span class="invalid-feedback" role="alert">
                                                                    <strong>{{ $message }}</strong>
                                                                </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                     
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label>Fully Paid</label>
                                                                <div class="row ">
                                                                    <div class="form-check ml-5">
                                                                        <input class="form-check-input" type="radio" name="paid_status" id="exampleRadios1" checked  value="0" >
                                                                        <label class="form-check-label" for="exampleRadios1">
                                                                            Paid
                                                                        </label>
                                                                    </div>
                                                                    <div class="form-check ml-5">
                                                                        <input class="form-check-input" type="radio" name="paid_status" id="exampleRadios2"  value="1" >
                                                                        <label class="form-check-label" for="exampleRadios2">
                                                                           Unpaid 
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                        <div class="row my-3">
                                                        <div class="col-md-12 text-center">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                            <input type="submit" value="@lang("message.confirm")" class="btn btn-success">
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
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
                                                    @lang("message.header.total_price")
                                                </th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id='addr0'>
                                                <td>
                                                1 <br> <span class="fa fa-search mt-3" id="show_search"></span>
                                                </td>
                                                <td>
                                                    <div class="row">
                                                    
                                                        <div class="col-md-12 mb-3"  id="hide_search">
                                                            <input type="search" id="search" autocomplete="off" name="search"  placeholder="search" class="form-control">
                                                        </div>
            
                                                        <div class="col-12">
                                                            <select class="form-control custom-select" id="item_id" name="item_id[]"  required>
                                                                <option value="">@lang("message.header.choose_item")</option>
                                                                @forelse($item as $data)
                                                                @php
                                                                $shop = App\Models\ShopStorage::where('item_id',$data->id)->first();
                                                                @endphp
                                                                <option value="{{$data->id}}">{{$data->name }} / ({{$shop ? $shop->qty : 0}}) </option>
                                                                @empty<p>@lang("message.header.there_is_no_data")</p>
                                                                @endforelse
                                                            </select>   
                                                           
                                                        </div>
                                                    </div>                                        
                                                </td>
                                                <td>
                                                    <input type="number" id="numeric_value" name="qty[]" class="form-control  @error('qty') is-invalid @enderror" placeholder='Qty' >
                                                </td>
                                                <td>
                                                    <input type="number" id="aa" name="price[]" class="form-control  @error('price') is-invalid @enderror" placeholder='Rate Per Unit' >
                                                </td>
                                                {{-- <td>
                                                    <input type="number" id="discount" name="discount[]" value="0" class="form-control  @error('discount') is-invalid @enderror" placeholder='Discount' >
                                                </td> --}}
                                                <td>
                                                    <input type="number" id="net_price" name="net_price[]" class="form-control  @error('net_price') is-invalid @enderror" placeholder="Total Price" >
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
                                {{-- <input type="submit" value="@lang("message.confirm")" class="btn btn-success"> --}}
                                <a title="Add Credit Report"  data-toggle="modal" data-target="#exampleModal" class="btn btn-primary text-white action-btn">Pay</a>
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

{!! JsValidator::formRequest('App\Http\Requests\SellItem','#create') !!}
<script>
     $(document).ready(function(){
      $('#hide_search').hide();

      var items = {!! json_encode($item) !!};
      var i=1;
      var text = "";
    for (var l=0 ; l < items.length; l++) {
    text +=  '<option value='+items[l].id+'>'+items[l].name+'</option>';
    } 

     $("#add_row").click(function(){
        $('#addr'+i).html("<td>"+ (i+1) +" <br> <span class='fa fa-search mt-3' id='show_search"+i+"'></span> </td><td><div class='row'><div class='col-md-12 mb-3' id='hide_search"+i+"' ><input type='search' class='form-control' id='search"+i+"' autocomplete='off' name='search' placeholder='search' ></div><div class='col-md-12'><select class='custom-select' id='item_id"+i+"' name='item_id[]"+i+"' required><option value=''>Choose Item Category</option>"+text+"</select></td></div></div><td><input  name='qty[]"+i+"' type='number' id='numeric_value"+i+"' autofocus='autofocus' placeholder='Qty'  class='form-control input-md'></td><td><input  id='aa"+i+"' name='price[]"+i+"' autofocus='autofocus' type='number' placeholder='Rate Per Unit'  class='form-control numeric_value"+i+"  input-md'></td><td><input  name='net_price[]"+i+"' type='number' placeholder='Total Price' id='net_price"+i+"' class='form-control  input-md'></td>");
      $('#tab_logic').append('<tr id="addr'+(i+1)+'"></tr>');

    var a = i;

    $('#numeric_value'+a).keyup(function() {
    var price = $("#aa"+a).val();
    // var discount = $("#discount"+a).val();
    var sum = 0;
    // sum = (Number($(this).val()) * price ) -discount ;
    sum = (Number($(this).val()) * price );
    $('#net_price'+a).val(sum);
    });

    $('#numeric_value'+a).keyup(function() {  
    var qty3 = $('#total_qty').val();
    var qty2 = Number($(this).val());
    var total_qty = parseInt(qty3) + parseInt(qty2) ; 
    $('#total_qty').val(total_qty);

    var net_price = $('#net_price').val();
    var net = $('#net_price'+a).val();
    var total_net = parseInt(net) + parseInt(net_price) ; 
    $('#total_grand').val(total_net);
    $('#paid_amount').val(total_net);

    });

    $('#discount'+a).on('change',function() {  
    var discount1 = $('#discount').val();
    var discount2 = $('#discount'+a).val();
    var total_dis = parseInt(discount1) + parseInt(discount2) ; 
    $('#total_discount').val(total_dis);

    var origin = $('#net_price').val();
    var net = $('#net_price'+a).val();
    var total = origin + net ;
    $('#total_grand').val(total);
    $('#paid_amount').val(total);
    });

    $('#discount'+a).keyup(function() {
    var price = $("#aa"+a).val();
    var qty = $("#numeric_value"+a).val();
    var sum = 0;
    sum = (qty * price ) - Number($(this).val())  ;
    $('#net_price'+a).val(sum);
    }); 

    $('#search'+a).change(function(e) {
            let search = $(this).val();
            $.get('/get_item?search=' + search, function(data) {
                    $('#item_id'+a).empty();
        $('#item_id'+a).append('<option disabled selected>'+ 'Choose Item' + '</option>');
                    $.each(data, function( key, value ) {
                      $('#item_id').append('<option value="'+value.id+'" >'+value.name+' / ('+value.qty+') </option>');
            });
        });
    });

    $('#item_id'+a).on('change', function(e) {  
        let item = $(this).val();
        $.get('/get_item?item=' + item, function(data) {
                $('#aa'+a).empty();
        $('#aa'+a).val(data.wholesale_price);
        $('#hide_search'+a).hide();

    });
});

    $('#hide_search'+a).hide();
    $('#show_search'+a).on('click',function(e){
    $('#hide_search'+a).show();
    
})

      i++; 
  });

     $("#delete_row").click(function(){
         if(i>1){
         $("#addr"+(i-1)).html('');

         i--;
         }
     });

});

$('#numeric_value').keyup(function() {  
    var price = $("#aa").val();
    // var discount = $("#discount").val();
    var sum = 0;
    // sum = (Number($(this).val()) * price ) - discount  ;
    sum = (Number($(this).val()) * price );

    $('#net_price').val(sum);
    }); 

    // $('#discount').keyup(function() {
    // var price = $("#aa").val();
    // var qty = $("#numeric_value").val();
    // var sum = 0;
    // sum = (qty * price ) - Number($(this).val())  ;
    
    // $('#net_price').val(sum);
    // }); 

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
            $('#aa').val(data.wholesale_price);
            $('#hide_search').hide();
        });
    });

$('#show_search').on('click',function(e){
    $('#hide_search').show();
})  

$('#numeric_value').keyup(function() {  
  var qty = $('#numeric_value').val();
  $('#total_qty').val(qty);

  var net_price = $('#net_price').val();
  $('#total_grand').val(net_price);
  $('#paid_amount').val(net_price);

  var discount = $('#discount').val();
  $('#total_discount').val(discount);
});

$('#discount').on('change',function() {  
  var discount = $('#discount').val();
  var grand = $('#total_grand').val();
  var total = grand - discount;
  $('#total_grand').val(total);
  $('#paid_amount').val(total);
});

$('#paid_amount').keyup(function() {  
  var paid = $('#paid_amount').val();
  var origin = $('#total_grand').val();
  var credit = parseInt(origin) - parseInt(paid);
  $('#credit_amount').val(credit);
});


</script>
@endsection
