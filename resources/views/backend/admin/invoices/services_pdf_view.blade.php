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
        color: #333;
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
          {{-- <img width="100px" height="100px" src="data:image/png;base64,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
          " alt="" class="src"> --}}

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
                    <th>Service Name</th>
                    <th >Description</th>
                    <th>Service Charges (MMK)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{$service_name}}</td>
                    <td style="white-space:pre-wrap !important;"> {{$service_description}}</td>
                    <td> {{$service_charges}}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="total_invoice ">
                <h2 class="total_invoice ">
                    <span class="total"> Invoice Total: </span>  {{$net_price}}  MMK
                </h2>

        {{-- <h3> Address</h3> --}}
        {{-- <p>Nay Pyi Taw - H-34 & H-35, Yazathigaha Road, Dekkhina Thiri Township, Hotel Zone(1) </p> --}}
        {{-- <p>(Hotline) : +95-67-8106655, Tel: +95-977900971-2,067-419113-5</p> --}}
        {{-- <p>Website:https//www.apexhotelmyanmar.com</p> --}}
    </div>
</body>

</html>
