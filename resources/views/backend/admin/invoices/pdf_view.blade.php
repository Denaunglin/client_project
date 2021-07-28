<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> {{$title}} </title>
</head>

<style>
    body{ 
        font-family: 'Tharlon' !important;
    }
    /* h1 {
        color: #43b1f0;
    } */

    /* h1 span {
        color: #43b1f0 !important;
    }

    h3 {
        color: #43b1f0;

    } */

    h2 {
        color: #0f212c;
    }

    .total_invoice span {
        font-size: 16px;
        color: black;
    }

    .total_invoice {
        float: right;

        background-color: #f7f7f7;
    }

    .table {
        margin-top: 0px;
        background-color: #f7f7f7;
    }

    .title-left {
        float: left;
    }

    .title-right {
        float: right;
    }

    .table tr td {
        /* border:1px solid gray; */
        font-size: 12px;
        padding-right: 50px;
        padding-left: 70px;
        padding-top: 5px;
        padding-bottom: 5px;
    }

    .table tr th {
        color: black;   
        border: 1px solid black;
        font-size: 14px;
    }

    .table tr td {
        border: 1px solid gray;
    }

    p {
        font-size: 15px;
        line-height: 1px;

    }
</style>

<body>
    <div class="table-responsive">
        <table class="table table-bordered ">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Qty</th>
                    <th>Rate Per Unit (MMK)</th>
                    <th>Total(MMK) </th>
                </tr>
            </thead>
            <tbody>
                @foreach($item_data as $data)
                <tr>
                    <td> 
                      {{$data['item_name']}}
                     </td>
                    <td>
                        {{$data['item_qty']}}
                    </td>
                    <td> 
                        {{number_format($data['price'])}}
                    </td>
                    <td> 
                        {{number_format($data['net_price'])}}
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5"></td>
                </tr>
                <tr class="text-left">
                    <td></td>
                    <td></td>
                    <td>@lang("message.header.total_qty")</td>
                    <td>{{$total_qty}}</td>
                </tr>
                {{-- <tr class="text-left">
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
                </tr> --}} 
                @if($discount != 0)
                <tr class="text-left">
                    <td></td>
                    <td></td>
                    <td>Total Discount </td>
                    <td>{{$discount}} MMK </td>
                </tr>
               
                @endif
                <tr class="text-left">
                    <td></td>
                    <td></td>
                    <td>@lang("message.grand_total") </td>
                    <td>{{$total_amount}} MMK</td>
                </tr> 
                @if($credit_amount != 0)
                <tr class="text-left">
                    <td></td>
                    <td></td>
                    <td>Paid Amount </td>
                    <td>{{$paid_amount}} MMK </td>
                </tr>
                <tr class="text-left">
                    <td></td>
                    <td></td>
                    <td>Credit Amount </td>
                    <td>{{$credit_amount}} MMK </td>
                </tr>
                @endif
            </tfoot>
        </table>
    </div>

    {{-- <div class="total_invoice ">
                <h2 class="total_invoice ">
                    <span class="total"> Invoice Total: </span>  {{$total_price}} MMK 
                </h2> --}}
        {{-- <h3> Address</h3> --}}
        {{-- <p>Nay Pyi Taw - H-34 & H-35, Yazathigaha Road, Dekkhina Thiri Township, Hotel Zone(1) </p> --}}
        {{-- <p>(Hotline) : +95-67-8106655, Tel: +95-977900971-2,067-419113-5</p> --}}
        {{-- <p>Website:https//www.apexhotelmyanmar.com</p> --}}
    {{-- </div> --}}
</body>

</html>
