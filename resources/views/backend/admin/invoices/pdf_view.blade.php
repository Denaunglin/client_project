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
    h1 {
        color: #43b1f0;
    }

    h1 span {
        color: #43b1f0 !important;
    }

    h3 {
        color: #43b1f0;

    }

    h2 {
        color: #0f212c;
    }

    .total_invoice span {
        font-size: 16px;
        color: black;
    }

    .total_invoice {
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
        font-size: 14px;
        padding-right: 70px;
        padding-left: 70px;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .table tr th {
        background-color: #43b1f0;
        color: white;
        border: 1px solid black;
        font-size: 18px;
    }

    .table tr td {
        border: 1px solid black;
    }

    p {
        font-size: 15px;
        line-height: 1px;

    }
</style>

<body>
    <div>

        <h2 style="align:center" style="margin-left:40%"><span>Butterfly</span> </h2>
        <h3>INVOICE </h3>
    </div>
   <div class="invoice-title">
        <div class="title-left" style="width:70%;">
            <p class="date"> Date issued : {{$today_date}}</p>
            <p>Invoice no : #{{$invoice_no}} </p>
            <p></p>
            <p></p>
        </div>
        <div class="title-right" style="width:30%;"> 
            <p style="font-family: Tharlon"> {{$shop_name}} , </p>
            <p> Email : {{$shop_email}}</p>
            <P>PH : {{$shop_phone}} ,</P>
            <p>  Address : {{$shop_address}} </p>

        </div>
    </div>

    <hr>

    <div class="table-responsive">
        <table class="table table-bordered ">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Qty</th>
                    <th>Rate Per Unit (MMK)</th>
                    <th>Discount</th>
                    <th>Total(MMK) </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td> 
                        @foreach($item_name as $data)
                            {{$data}}
                        @endforeach
                     </td>
                    <td>
                        @foreach($qty as $data)
                        {{$data}}
                        @endforeach
                    </td>
                    <td>
                        @foreach($price as $data)
                        {{$data}}
                        @endforeach
                    </td>
                    <td>
                        @foreach($discount as $data)
                        {{$data}}
                        @endforeach
                    </td>
                    <td> 
                        @foreach($net_price as $data)
                        {{$data}}
                        @endforeach
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="total_invoice ">
                <h2 class="total_invoice ">
                    <span class="total"> Invoice Total: </span>  {{$net_price}} MMK 
                </h2>

        {{-- <h3> Address</h3> --}}
        {{-- <p>Nay Pyi Taw - H-34 & H-35, Yazathigaha Road, Dekkhina Thiri Township, Hotel Zone(1) </p> --}}
        {{-- <p>(Hotline) : +95-67-8106655, Tel: +95-977900971-2,067-419113-5</p> --}}
        {{-- <p>Website:https//www.apexhotelmyanmar.com</p> --}}
    </div>
</body>

</html>
