@extends('backend.admin.layouts.app')
@section('meta_title', 'Add Opening Item')
@section('page_title')
@lang("message.header.add_opening_item")
@endsection
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('page_title_buttons')
<div class="d-flex justify-content-end">
    <a href="{{route('admin.items.create')}}" title="Add Category" class="btn btn-primary action-btn">@lang("message.header.add_item")</a>
</div>
@endsection

@section('content')
@include('layouts.errors_alert')
<div class="row">
    <div class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <form action="{{ route('admin.opening_items.store') }}" method="post" id="create" enctype="multipart/form-data">
                    @csrf
                    <div class="container">
                        <div class="row clearfix">
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
                                                    <option value="">@lang("message.header.choose_item_category")</option>
                                                    @forelse($item as $data)
                                                    <option value="{{$data->id}}">{{$data->name }}</option>
                                                    @empty<p>@lang("message.header.there_is_no_data")</p>
                                                    @endforelse
                                                </select>                                             
                                            </td>
                                            <td>
                                                <input type="number" id="qty" name="qty" class="form-control  @error('qty') is-invalid @enderror" placeholder='Qty' >
                                            </td>
                                            <td>
                                                <input type="number" id="price" name="price" class="form-control  @error('price') is-invalid @enderror" placeholder='Rate Per Unit' >
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
                        <a id="add_row" class="btn btn-default pull-left">@lang("message.header.add_row")</a><a id='delete_row' class="pull-right btn btn-default">Delete Row</a>
                    </div>  
                  
                    <div class="row my-3">
                        <div class="col-md-12 text-center">
                            <a href="{{ route('admin.opening_items.index') }}" class="btn btn-danger mr-3">@lang("message.cancel")</a>
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
{!! JsValidator::formRequest('App\Http\Requests\BuyingItemRequest', '#create') !!}
<script>
  
    $(document).ready(function(){
      var items = {!! json_encode($item) !!};
      var i=1;
      var text = "";
    for (var l=0 ; l < items.length; l++) {
    text +=  '<option value='+items[l].id+'>'+items[l].name+'</option>';    
    } 
     $("#add_row").click(function(){
      $('#addr'+i).html("<td>"+ (i+1) +"</td><td><select class='custom-select' id='item_id' name='item_id[]"+i+"' required><option value=''>Choose Item Category</option>"+text+"</select></td><td><input  name='qty"+i+"' type='number' placeholder='Qty'  class='form-control input-md'></td><td><input  name='price"+i+"' type='number' placeholder='Rate Per Unit'  class='form-control input-md'></td><td><input  name='discount"+i+"' type='number' placeholder='Discount'  class='form-control input-md'></td><td><input  name='net_price"+i+"' type='number' placeholder='Total Price'  class='form-control input-md'></td>");
      $('#tab_logic').append('<tr id="addr'+(i+1)+'"></tr>');
      i++;  
  });
     $("#delete_row").click(function(){
         if(i>1){
         $("#addr"+(i-1)).html('');
         i--;
         }
     });

}); 
   
</script>

@endsection
