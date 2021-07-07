@extends('backend.admin.layouts.app')
@section('meta_title', 'Dashboard')
@section('page_title')
@lang("message.header.dashboard")
@endsection
@section('dashboard-active','mm-active')

@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('extra_css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
<style>
   
    .gambar {
        width: 100%;
        height: 175px;
        padding: 0.9rem 0.9rem
    }
    @media only screen and (max-width: 600px) {
        .gambar {
            width: 100%;
            height: 100%;
            padding: 0.9rem 0.9rem
        }
    }

    html {
        overflow: scroll;
        overflow-x: hidden;
    }

    ::-webkit-scrollbar {
        width: 0px;
        /* Remove scrollbar space */
        background: transparent;
        /* Optional: just make scrollbar invisible */
    }

    /* Optional: show position indicator in red */
    ::-webkit-scrollbar-thumb {
        background: #FF0000;
    }

    .cart-btn {
        position: absolute;
        display: block;
        top: 5%;
        right: 5%;
        cursor: pointer;
        transition: all 0.3s linear;
        padding: 0.6rem 0.8rem !important;

    }

    .productCard {
        cursor: pointer;

    }

    .productCard:hover {
        border: solid 1px rgb(172, 172, 172);

    }

</style>
@endsection
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" style="min-height:85vh">
                <div class="card-header bg-white">
                    <form action="{{ url('admin/transcation') }}" method="get">
                        <div class="row mt-3 sm-mb-3">
                            <div class="col-md-3">
                                <h4 >@lang("message.header.item")</h4>
                            </div>
                            <div class="col-md-3 mb-3 text-right">
                                <select name="search_category" id=""  class="form-control from-control-sm" style="font-size: 12px">
                                    <option value="" holder>@lang("message.header.filter_category")</option>
                                    @foreach($item_category as $data)
                                        <option value="{{$data->id}}">{{$data->name}}</option>
                                    @endforeach 
                                </select>
                            </div>
                            <div class="col-md-3 mb-3"><input type="text" name="search_item"
                                    class="form-control col-sm-12 float-right" autocomplete='off' 
                                    placeholder="@lang("message.header.search_product")..." onblur="this.form.submit()"></div>
                            <div class="col-md-3"><button type="submit"class="btn btn-primary btn-block float-right btn-block">@lang("message.header.search_product")</button></div>
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach ($products as $product)
                        <div style="border:1px solid rgb(243, 243, 243)"  class="mb-4 col-md-4">
                            <div class="productCard">
                                <div class="view overlay">
                                    <form action="{{url('admin/transcation/addproduct', $product->id)}}" method="POST">
                                        @csrf
                                        @php 
                                        $qty = $product->shopstorage ? $product->shopstorage->qty : 0 ;
                                        @endphp
                                        <span class="text-info">({{$product->shopstorage ? $product->shopstorage->qty : 0}})</span>
                                        @if($qty == 0)
                                        <button type="button" class="btn btn-danger btn-sm float-right "><i class="fas fa-cart-plus"></i></button>
                                        
                                        @else
                                        <button type="submit" class="btn btn-primary btn-sm float-right "><i class="fas fa-cart-plus"></i></button>
                                       
                                        @endif
                                    </form>
                                </div>
                                <div class="card-body">
                                    <label class="card-text text-center font-weight-bold"
                                        style="text-transform: capitalize;">
                                        {{ Str::words($product->name,100) }}    </label>
                                    <hr>
                                        <p class="card-text text-center">{{number_format($product->retail_price) }} MMK. 
                                    </p>
                                    
                                    <hr>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div>{{ $products->links() }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" >
                <div class="card-header bg-white">
                    <div class="col-md-12 text-center">
                        <div class="row">
                            <div class="col-md-12">
                                <h4>  <i class="fa fa-cart-plus mr-3" style="font-size: 20px" ></i>@lang("message.header.cart")</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    
                    <div class="col-md-12">
                        <div class="form-group">
                            <form action="{{ url('admin/transcation') }}" method="get">
                            <select name="customer" class="form-control" id="customer"  onchange="this.form.submit()">
                                <option value="0">Default Customer</option>
                                @forelse($customer as $data)
                                <option @if(request()->customer ? request()->customer == $data->id : 0) selected @endif value="{{$data->id}}">{{$data->name}} / {{$data->phone}} / {{$data->address}} </option>
                                @empty
                                <option value="0">No Customer Data</option>
                                @endif
                            </select></form>
                        </div>
                    </div>
                    <div style="overflow-y:auto;min-height:53vh;max-height:53vh" class="mb-3">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th width="10%">@lang("message.header.no")</th>
                                    <th width="30%">@lang("message.header.item")</th>
                                    <th width="30%">@lang("message.header.qty")</th>
                                    <th width="30%" class="text-right">@lang("message.header.sub_total")</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(session('cart'))
                                @php
                                $no=1
                                @endphp
                                
                                @forelse($cart as $index=>$item)
                                <tr>
                                    <td>
                                        <form action="{{url('admin/transcation/removeproduct',$item['id'])}}"
                                            method="POST">
                                            @csrf
                                            {{$no++}} <br><a onclick="this.closest('form').submit();return false;"><i class="fas fa-trash" style="color: rgb(134, 134, 134)"></i></a>
                                        </form>
                                    </td>
                                    <td>{{Str::words($item['name'],3)}} <br>MMK.
                                        {{ number_format($item['price']) }}
                                    </td>
                                    <td class="font-weight-bold">
                                        <form action="{{url('admin/transcation/decreasecart', $item['id'])}}"
                                            method="POST" style='display:inline;'>
                                            @csrf
                                            <button class="btn btn-sm btn-info"
                                                style="display: inline;padding:0.4rem 0.6rem!important"><i
                                                    class="fas fa-minus"></i></button>
                                        </form>
                                        <a style="display: inline">{{$item['quantity']}}</a>
                                        <form action="{{url('admin/transcation/increasecart', $item['id'])}}"
                                            method="POST" style='display:inline;'>
                                            @csrf
                                            <button class="btn btn-sm btn-primary"
                                                style="display: inline;padding:0.4rem 0.6rem!important"><i
                                                    class="fas fa-plus"></i></button>
                                        </form>
                                    </td>
                                    <td class="text-right">MMK. {{number_format($item['sub_total']) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Empty Cart</td>
                                </tr>
                                @endforelse
                                @else
                                <tr>
                                    <td colspan="4" class="text-center">Empty Cart</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th> 
                                <form action="{{ url('admin/transcation') }}" method="get">
                                 <input type="checkbox" value="1"  name="credit_check"  {{ request()->credit_check == 1 ? "checked" : ""}}  onclick="this.form.submit()" id="credit_check">
                                </form>
                            </th>
                            <th class="text-right"> @lang("message.header.credit")  </th>
                        </tr>
                        <tr>
                            <th width="60%">@lang("message.header.total_qty")</th>
                            <th width="40%" class="text-right">
                                {{number_format($data_total['total_qty']) }} </th>
                        </tr>
                        <tr>
                            <th width="60%">@lang("message.header.sub_total")</th>
                            <th width="40%" class="text-right">MMK.
                                {{number_format($data_total['sub_total']) }} </th>
                        </tr>
                        {{-- <tr>
                            <th>
                                <form action="{{ url('admin/transcation') }}" method="get">
                                     10%
                                    <input type="checkbox" {{ $data_total['tax'] > 0 ? "checked" : ""}} name="tax"
                                        value="true" onclick="this.form.submit()">
                                </form>
                            </th>
                            <th class="text-right">MMK.
                                {{$data_total['tax'] }}</th>
                        </tr> --}}
                       
                        <tr>
                            <th>@lang("message.header.total_price")</th>
                            <th class="text-right font-weight-bold">MMK.
                                {{ number_format($data_total['total']) }}</th>
                        </tr>
                    </table>
                    <div class="row">
                        <div class="col-sm-6 mb-3">
                            <form action="{{ url('admin/transcation/clear') }}" method="POST">
                                @csrf
                                <button class="btn btn-info btn-lg btn-block" style="padding:1rem!important"
                                    onclick="return confirm('Do you want to clear cart ?');"
                                    type="submit"> Clear</button>
                            </form>
                        </div>
                      
                        <div class="col-sm-6">
                            @if($data_total['credit_check'] == 1)
                            <a class="btn btn-success btn-lg btn-block" style="padding:1rem!important"
                               href="{{ route('admin.credit_reports.create') }}" >Credit Save</a>
                            @else
                            @php 
                            $customer = request()->customer ? request()->customer : 0;
                            @endphp
                            <a class="btn btn-success btn-lg btn-block" style="padding:1rem!important"
                            href="{{ url('admin/final_pays?customer='.$customer) }}" >Check Out</a>

                            {{-- <a class="btn btn-success btn-lg btn-block" style="padding:1rem!important"
                            href="{{ url('admin/index/sell_items') }}" >Pay</a> --}}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script>

    function cek(bayar, jumlah) {
        const saveButton = document.getElementById("saveButton");   

        if (bayar < jumlah) {
            saveButton.disabled = true;
        } else {
            saveButton.disabled = false;
        }
    }

</script>
@endsection
