@extends('backend.admin.layouts.app')

@section('meta_title', 'Room Plan')
@section('page_title', 'Room Plan')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('extra_css')
<style>
    a{
        color:black;
    }
    .modal-dialog {
        top: 200px !important;
    }

    .modal-backdrop {
        z-index: 9999999 !important;
        position: relative;

    }

    .modal-backdrop.show {
        opacity: 0 !important;
    }
</style>
@endsection
@section('page_title_buttons')

@endsection

@section('content')
<link href="{{asset('assets/css/backend_room_plan.css')}}" rel="stylesheet">

<div class="pb-4">

    <form method="get" action="/admin/plan/search">
        <div class="row">
            <div class="col-lg-4">
                <div class="input-group">
                    <input type="text" class="form-control" name="date" value="{{request()->date}}" id="demo" />
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Check</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

            {{-- <button class="btn print btn-primary mt-3" onclick="window.print()">Print this page</button> --}}
 <button class="btn btn-primary mt-3 print">Print Room Plan</button>
</div>
        <div class="row">
            <div class="print-data">
                <div class="col-md-12">
                    <div class="main-card mb-3 card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="align-middle table table-bordered room-plan-table">
                                    <thead>
                                      <p>Date: {{request()->date}} | Time : {{$time}} </p> 
                                        <tr class="text-center">
                                            <th colspan="16" ><div class="title-center"><span class="apex">Apex</span>&nbsp;<span class="hotel">Hotel</span> </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach($first_floor_rooms as $room_14_group)
                                        <tr>
                                            @if($loop->first)
                                            <td rowspan="3" class="text-center vrt ">First Floor </td>
                                            @endif

                                            @php
                                            $room_7_groups = collect($room_14_group)->chunk(7);
                                            @endphp
                                            @foreach($room_7_groups as $room_7_group)
                                            @foreach($room_7_group as $room)
                                            @php
                                            $bg_color = '';
                                            $bedtype = '-';
                                            if($room->room){
                                            if($room->room->room_type == 1){
                                            $bg_color = 'ambassador';
                                            }else if($room->room->room_type == 2){
                                            $bg_color = 'standard';
                                            }else if($room->room->room_type == 3){
                                            $bg_color = 'superior';
                                            }else if($room->room->room_type == 4){
                                            $bg_color = 'deluxe';
                                            }else if($room->room->room_type == 5){
                                            $bg_color = 'e_suite';
                                            }else if($room->room->room_type == 6){
                                            $bg_color = 'suite';
                                            }
                                            $bedtype = $room->room->bedtype ? $room->room->bedtype->name : '-';
                                            }
                                            @endphp

                                        <td class="text-center {{$bg_color}} shadow">
                                                @php
                                                $schedule_room = collect($check_room)->where('room_no', $room->id)->first();
                                                @endphp
                                                @if($room->maintain==1)
                                                 <p>{{$room->room_no}}</p>
                                                <h5 class="text-danger  room_close bg-warning"> Close</h5>
                                                @else
                                                    @if($schedule_room)
                                                    <a href="{{url('admin/roomschedules/'.$schedule_room->id.'/edit')}}">
                                                        <p>{{$room->room_no}}</p>
                                                        <span>{{$bedtype}}</span>
                                                        @if($schedule_room->status == 1)
                                                        <span class="badge badge-danger">Taken</span>
                                                        @elseif($schedule_room->status == 2)
                                                        <span class="badge badge-primary">Checkin</span>
                                                        @elseif($schedule_room->status == 3)
                                                        <span class="badge badge-warning">Checkout <br> (No Cleaning)</span>
                                                       
                                                        @endif
                                                    </a>
                                                    @else
                                                    <a href="#" class="add-schedule d-block" data-room-id="{{$room->room_id}}"
                                                        data-room-no="{{$room->room_no}}">
                                                        <p>{{$room->room_no}}</p>
                                                        <span>{{$bedtype}}</span>
                                                    </a>
                                                    @endif
                                                @endif
                                            </td>
                                            @endforeach

                                            @if(!$loop->last)
                                            <td class="text-center vrt ">Lobby </td>
                                            @endif
                                            @endforeach
                                        </tr>
                                        @if(!$loop->last)
                                        <tr>
                                            <td class="bg-white text-center" colspan="3">Walk Way Deluxe</td>
                                            <td class="bg-white text-center" colspan="4">Walk Way Excuitve Suite</td>
                                            <td class="bg-white"></td>
                                            <td class="bg-white text-center" colspan="7">Walk Way Superior</td>
                                        </tr>
                                        @endif
                                        @endforeach

                                        <tr>
                                            <td class="bg-secondary" colspan="16"></td>
                                        </tr>

                                        @foreach($ground_floor_rooms as $room_14_group)
                                        <tr>
                                            @if($loop->first)
                                            <td rowspan="3" class="text-center vrt ">Ground Floor </td>
                                            @endif
                                            @php
                                            $room_7_groups = collect($room_14_group)->chunk(7);
                                            @endphp
                                            @foreach($room_7_groups as $room_7_group)
                                            @foreach($room_7_group as $room)
                                            @php
                                            $bg_color = '';
                                            $bedtype = '-';
                                            if($room->room){
                                            if($room->room->room_type == 1){
                                            $bg_color = 'ambassador';
                                            }else if($room->room->room_type == 2){
                                            $bg_color = 'standard';
                                            }else if($room->room->room_type == 3){
                                            $bg_color = 'superior';
                                            }else if($room->room->room_type == 4){
                                            $bg_color = 'deluxe';
                                            }else if($room->room->room_type == 5){
                                            $bg_color = 'e_suite';
                                            }else if($room->room->room_type == 6){
                                            $bg_color = 'suite';
                                            }
                                            $bedtype = $room->room->bedtype ? $room->room->bedtype->name : '-';
                                            }
                                            @endphp

                                            <td class="text-center {{$bg_color}} shadow">
                                                @php
                                                $schedule_room = collect($check_room)->where('room_no', $room->id)->first();
                                                @endphp
                                                @if($room->maintain==1)
                                                  <p>{{$room->room_no}}</p>
                                                <h5 class="text-danger room_close bg-warning"> Close</h5>
                                                @else
                                                        @if($schedule_room)
                                                        <a href="{{url('admin/roomschedules/'.$schedule_room->id.'/edit')}}">
                                                            <p>{{$room->room_no}}</p>
                                                            <span>{{$bedtype}}</span>
                                                            @if($schedule_room->status == 1)
                                                            <span class="badge badge-danger">Taken</span>
                                                            @elseif($schedule_room->status == 2)
                                                            <span class="badge badge-primary">Checkin</span>
                                                            @elseif($schedule_room->status == 3)
                                                            <span class="badge badge-warning"> Checkout <br> (No Cleaning)</span>  
                                                                             
                                                            @endif
                                                        </a>
                                                        @else
                                                        <a href="#" class="add-schedule d-block" data-room-id="{{$room->room_id}}"
                                                            data-room-no="{{$room->room_no}}">
                                                            <p>{{$room->room_no}}</p>
                                                            <span>{{$bedtype}}</span>
                                                        </a>
                                                        @endif
                                                @endif
                                                    
                                            </td>
                                            @endforeach
                                            @if(!$loop->last)
                                            <td class="text-center vrt ">Lobby </td>
                                            @endif
                                            @endforeach
                                        </tr>
                                        @if(!$loop->last)
                                        <tr>

                                            <td class="bg-white text-center" colspan="7">Walk Way Superior</td>
                                            <td class="bg-white"></td>
                                            <td class="bg-white text-center" colspan="7">Walk Way Suite</td>
                                        </tr>
                                        @endif
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                <div class="col-md-8 offset-2 ">
                    <div class="main-card  card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="align-middle table table-bordered border-dark">
                                    <thead>
                                        <tr>
                                            <th class="e_suite" colspan="2">Room Type</th>
                                            <th class="e_suite">Bed Type</th>
                                            <th class="e_suite">Availiable Room</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="text-center">
                                            <td class="standard" colspan="2"><label>Standard</label></td>

                                            <td><label>One Single</label></td>
                                            <td>2</td>
                                        </tr>
                                        <tr class="text-center">
                                            <td class="superior" rowspan="2" colspan="2"><label>Superior</label></td>
                                            <td><label>Twin(13)</label></td>
                                            <td rowspan="2"><label>27</label></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td><label>Double(14)</label></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td class="deluxe" rowspan="2" colspan="2"><label>Deluxe</label></td>
                                            <td><label>Twin(2)</label></td>
                                            <td rowspan="2"><label>6</label></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td><label>Double(4)</label></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td class="e_suite" colspan="2" rowspan="2"><label>Executive Suite</label></td>
                                            <td rowspan="2"><label>1-Double 1-Single</label></td>
                                            <td rowspan="2"><label>8</label></td>
                                        </tr>
                                        <tr></tr>
                                        <tr class="text-center">
                                            <td class="suite" colspan="2"><label>Suite</label></td>
                                            <td><label>1-Double</label></td>
                                            <td><label>12</label></td>
                                        </tr>
                                        <tr class="text-center" >
                                            <td colspan="2" class="ambassador"><label>Ambassador Suite</label></td>
                                            <td><label>1-Double</label></td>
                                            <td><label>1</label></td>
                                        </tr>
                                        <tr class="text-center">
                                            <td colspan="2"><label>Total</label></td>
                                            <td><label></label></td>
                                            <td><label>56</label></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
        </div>

 

@endsection

@section('script')
<script>
    $(document).ready(function(){
        $('#demo').daterangepicker({
            singleDatePicker: true,
            opens:'right',
            autoApply: true,
            locale: {
            format: 'YYYY-MM-DD'
            }
        });

        $(document).on('click', '.add-schedule', function (e) {
            e.preventDefault();
            var room_id = $(this).data('room-id');
            var room_no = $(this).data('room-no');

            swal({
                title:"Already have booking?",
                className : 'schedule-modal',
                buttons: {
                    default: "Cancel",
                    existing: "Existing Booking",
                    new: "New Booking"
                }
            })
            .then((value) => {
                switch (value) {
                    case "existing":
                        location.replace(`/admin/roomschedules/addbook/${room_id}/${room_no}`);
                        break;
                    case "new":
                        location.replace(`/admin/roomschedules/addplan/${room_id}/${room_no}`);
                        break;
                    default:
                }
            });
        });
    });


    $(document).on('click', '.print', function(e) {
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

                        .hotel {
                            color: green;
                        }

                        table {
                            width: 100%;
                            margin-bottom: 1rem;
                            background-color: gray;
                        }

                        .text-center {
                            text-align: center !important;
                        }

                        td {
                        border-bottom-width: 2px;
                        }

                        .table thead th {
                            vertical-align: bottom;
                            border-bottom: 2px solid #e9ecef;
                            border-bottom-width: 2px;
                        }

                        .room-plan-table th, .room-plan-table td {
                            max-width: 150px !important;
                            font-size: 10px;
                        }

                        .table-bordered th, .table-bordered td {
                            border: 1px solid #e9ecef;
                        }

                        .table td {
                            max-width: 150px !important;
                            font-size: 10px;
                            backgroud-color:blue;
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
                        
                        .table{
                            background-color:#edf0f2;
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
@include('backend.admin.layouts.assets.trash_script')
@endsection
