@extends('backend.admin.layouts.app')
@section('meta_title', 'Add Buying Item')
@section('page_title')
@lang("message.header.add_buying_item")
@endsection
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('extra_css')
<style>
       .modal-content {
        margin-top:100px !important;
        position: relative !important;
    }
    .modal{
        margin-top: 100px;  
    }
    .modal-backdrop{
        position: relative !important;

    }


    .modal-backdrop.show {
    opacity: 0 !important;
}
</style>
@endsection
@section('page_title_buttons')
<div class="d-flex justify-content-end">
       <a href="{{route('admin.suppliers.create')}}" title="Add User" class="btn btn-primary action-btn">Add Supplier</a>
</div>
@endsection
@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.buying_items.store') }}" method="post" id="create" enctype="multipart/form-data">
                    @csrf
                    <div class="container">
                        <div class="row clearfix">
                            <div class="col-md-4 mb-5">
                                <label for="">@lang("message.header.supplier")</label>
                                <select class="form-control custom-select" name="supplier" id="supplier">
                                    <option value="">@lang("message.header.select_supplier")</option>
                                    @forelse($supplier as $data)
                                     <option value="{{$data->id}}">{{$data->name}} / {{$data->address}}</option>
                                     @empty
                                     <option value="">@lang("message.header.there_is_no_data")</option>
                                    @endforelse
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
                                                    
                                                    <div class="col-md-12 mb-3" id="hide_search">
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
                                                <input type="number" id="numeric_value" name="qty[]" autofocus="autofocus" class="form-control  @error('qty') is-invalid @enderror" placeholder='Qty' >
                                            </td>
                                            <td>
                                                <input type="number" id="aa" name="price[]" autofocus="autofocus" class="form-control  @error('price') is-invalid @enderror" placeholder='Rate Per Unit' >
                                            </td>
                                          
                                            <td>
                                                <input type="number" id="net_price" name="net_price[]" class="form-control   @error('net_price') is-invalid @enderror" placeholder="Total Price" >
                                            </td>
                                        </td>
                                        </tr>
                                        <tr id='addr1'></tr>
                                      
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <a id="add_row" class="btn btn-default pull-left">@lang("message.header.add_row")</a><a id='delete_row' class="pull-right btn btn-default">@lang("message.header.delete_row")</a>
                    </div>  
                  
                    <div class="row my-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.buying_items.index') }}" class="btn btn-danger mr-3">@lang("message.cancel")</a>
                            <input type="@lang("message.submit")" value="Confirm" class="btn btn-success">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
{!! JsValidator::formRequest('App\Http\Requests\BuyingItemRequest', '#create') !!}
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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
    $('#addr'+i).html("<td>"+ (i+1) +" <br> <span class='fa fa-search mt-3' id='show_search"+i+"'></span> </td><td><div class='row'><div class='col-md-12 mb-3' id='hide_search"+i+"'><input type='search' class='form-control' id='search"+i+"' autocomplete='off' name='search' placeholder='search' ></div><div class='col-md-12'><select class='custom-select' id='item_id"+i+"' name='item_id[]"+i+"' required><option value=''>Choose Item Category</option>"+text+"</select></td></div></div><td><input  name='qty[]"+i+"' type='number' id='numeric_value"+i+"' autofocus='autofocus' placeholder='Qty'  class='form-control input-md'></td><td><input  id='aa"+i+"' name='price[]"+i+"' autofocus='autofocus' type='number' placeholder='Rate Per Unit'  class='form-control numeric_value"+i+"  input-md'></td><td><input  name='net_price[]"+i+"' type='number' placeholder='Total Price' id='net_price"+i+"' class='form-control  input-md'></td>");
    $('#tab_logic').append('<tr id="addr'+(i+1)+'"></tr>');

    var a = i;
    $('#numeric_value'+a).keyup(function() {
    var price = $("#aa"+a).val();
    var sum = 0;
    sum = price * Number($(this).val());
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
    var sum = 0;
    sum = price * Number($(this).val());
    
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
            $('#hide_search').hide();
        });
    });

    $('#show_search').on('click',function(e){
    $('#hide_search').show();

    
})
   
</script>

@endsection
