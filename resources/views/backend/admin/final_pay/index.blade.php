@extends('backend.admin.layouts.app')

@section('meta_title', 'Final Pay')
@section('page_title')
@lang("message.header.final_pay")
@endsection
@section('account-type-active','mm-active')
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
@section('content')

<div class="row">
    <div class="col-md-12 mb-3">
        {{-- <button class="btn btn-primary float-right mt-3 print">@lang("message.header.print_slip")</button> --}}
        <a type="submit" class="btn btn-success float-right btn-lg btn-sm" data-toggle="modal" data-target="#exampleModal" style="padding:1rem!important"
        href="{{ url('admin/index/sell_items?customer='.request()->customer) }}" >Pay</a>
    </div>

    <div class="col-md-12">
        <div class="print-data">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <div class="table-responsive">

                    <table class="align-middle table data-table table-bordered">
                        <thead>
                            <tr>
                                <th></th>
                                <th>@lang("message.header.item")</th>
                                <th>@lang("message.header.qty")</th>
                                <th>@lang("message.header.rate_per_unit")</th>
                                <th>@lang("message.header.sub_total")</th>
                                <th>@lang("message.header.discount")</th>
                                <th>@lang("message.header.total_price")</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pay_items as $data)
                            <tr class="text-center">
                            <td></td>
                            <td>{{$data['item_name']}}</td>
                            <td>{{$data['total_qty']}}</td>
                            <td>{{$data['rate_per_unit']}}</td>
                            <td>{{$data['sub_total']}}</td>
                            <td>{{$data['discount_amount']}}</td>
                            <td>{{$data['total']}}</td>
                             </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8"></td>
                            </tr>
                            <tr>
                                <td colspan="8"></td>
                            </tr>
                            <tr class="text-left">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>@lang("message.header.total_qty")</th>
                                <th>{{$grand_total_qty}}</th>
                            </tr>
                            <tr class="text-left">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>@lang("message.header.total_discount") </th>
                                @if($total_discount_percentage != 0)
                                <th>{{$total_discount_percentage}} %</th>
                                @else 
                                <th>{{$total_discount_amount}} MMK</th>
                                @endif
                            </tr>
                            @if($tax != 0)
                            <tr class="text-left">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>@lang("message.header.tax") </th>
                                <th>{{$tax}} %</th>
                            </tr>
                            @endif
                            <tr class="text-left">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th>@lang("message.grand_total") </th>
                                <th>{{$grand_total}} MMK</th>
                            </tr>
                        </tfoot>
                    </table>
                    <h5 class="text-center">Thank for shopping !</h5>
                </div>
            </div>
        </div>
        </div>
    </div>

    <div class="col-md-12">
         <div class="modal mt-5 fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header text-center">
                    <p class="text-center">
                        Would you like to print ?
                    </p>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                    <form action="{{ url('admin/index/sell_items?customer='.request()->customer) }}" id="myform" method="get" >
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-warning btn-block" >No</button>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary print  btn-block"  >
                                    Yes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
              </div>
            </div>
          </div>
    </div>
</div>

@endsection

@section('script')
<script>

    $(document).on('click', '.print', function(e) {
        document.getElementById("myform").submit(); 
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
