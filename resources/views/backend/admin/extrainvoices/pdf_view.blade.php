<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> {{$title}} </title>
</head>

<style>
    h1 {
        margin-left: 40%;
        color: green;
    }

    h1 span {
        color: red !important;
    }

    h3 {
        color: green;

    }

    h2 {
        color: #ffc107;
    }

    .total_invoice span {
        font-size: 16px;
        color: black;
    }

    .total_invoice {
        background-color: #f7f7f7;
    }

    .table {
        margin-top: 150px;
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
        font-size: 17px;
        padding-right: 60px;
        padding-left: 60px;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .table tr th {
        background-color: green;
        color: white;
        border: 1px solid black;
        font-size: 20px;
    }

    .table tr td {
        border: 1px solid black;
    }

    p {
        font-size: 17px;
    }
</style>

<body>
    <div>
        <img width="120px" height="100px" src="data:image/png;base64,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" alt="" class="src">
        <h1><span>Apex</span> Hotel</h1>
        <h3>INVOICE FOLIO</h3>
    </div>

    <div class="invoice-title">
        <div class="title-left">
            <p class="date"> Date issued : {{$today_date}}</p>
            {{-- <p>Invoice no : #{{$invoice_no}} </p> --}}
            <p>Booking no : {{$booking_no}}</p>
            <p>Invoice no : {{$invoice_no}}</p>
             @if($roomno != 0)
            <p>Room No : {{$roomno}} ( {{$roomtype}} / {{$bedtype}})</p>
            
            @endif

        </div>
        <div class="title-right">
            <p> Name : {{$client_name}}</p>
            <p> Email : {{$client_email}} </p>
        </div>
    </div>

    <hr>

    <div class="table-responsive">
        <table class="table table-bordered ">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Amount</th>
                </tr>
            </thead>

            {{-- NATIONALITY MM --}}

            @if($nationality==1)

            <tr>
                <td>Other Services  </td>
                <td> @foreach($otherservices as $data ) {{$data['name']}}({{$data['qty']}}),  @endforeach </td>
                <td> @foreach($otherservices as $data ) {{$data['total']}} MMK,   @endforeach </td>
            </tr>

            <tr style="background-color: #ffc107">
                <td colspan="2">Other Services Total </td>
                <td>{{$other_charges_total}} MMK </td>
            </tr>
          
            <hr>

            {{-- NATIONALITY FOREIGN --}}
            @else
         
              <tr>
                <td>Other Services  </td>
                <td> @foreach($otherservices as $data ) {{$data['name']}}({{$data['qty']}}),  @endforeach </td>
                <td> @foreach($otherservices as $data ) $ {{$data['total']}},  @endforeach </td>

            </tr>
             
            <tr style="background-color: #ffc107">
                <td colspan="2" >Other Services Total </td>
                <td> $ {{$other_charges_total}}  </td>
            </tr>
        
            @endif
            <hr>

        </table>
    </div>

    <div class="total_invoice">

        @if($nationality==1)
            <h2 class="total_invoice"> <span class="total"> Invoice Total: </span>{{$other_charges_total}} MMK </h2>
        @else
             <h2 class="total_invoice"> <span class="total">Invoice Total:</span> $ {{$other_charges_total}} </h2>
        @endif

        <h3> Address</h3>
        <p>Nay Pyi Taw - H-34 & H-35, Yazathigaha Road, Dekkhina Thiri Township, Hotel Zone(1) </p>
        <p>(Hotline) : +95-67-8106655, Tel: +95-977900971-2,067-419113-5</p>

        <p>Website:https//www.apexhotelmyanmar.com</p>
    </div>

</body>

</html>
