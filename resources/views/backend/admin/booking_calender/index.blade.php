@extends('backend.admin.layouts.app')
@section('meta_title', 'Daily Sell Rate')
@section('page_title', 'Daily Sell Rate')
@section('page_title_icon')
<i class="pe-7s-menu icon-gradient bg-ripe-malin"></i>
@endsection
@section('style')
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.css" />
<style>
@media only screen and (max-width: 991px) {
div h6 {
    font-size: 0.6rem !important;
}
td {
    font-size: 0.5rem !important;
}
}
@media only screen and (max-width: 767px) {
div h6 {
    font-size: 0.6rem !important;
}
td {
    font-size: 0.2rem !important;
}
}
@media only screen and (max-width: 425px) {
div h6 {
    font-size: 0.5rem !important;
}
td {
    font-size: 0.25rem !important;
}
   
}
    .fc-event,
    .fc-event-dot {
        padding: 5px 0 !important;
        background-color: #38cf69 !important;
        border-color: #38cf69 !important;
    }
    div h6{
        color:brown;
    }
     div b{
        color:#38cf69;
    }
    
    .swal2-title {
        font-size: 24px;
    }
    .Standard{
        color:#38cf69;
    }
    td {
        font-size: 12px;
    }
    .swal2-content table td {
        font-size: 16px;
    }

    .swal2-content table td:nth-of-type(1) {
        text-align: left !important;
    }

    .swal2-content table td:nth-of-type(2) {
        text-align: right !important;
    }
</style>
@endsection
@section('content')

<div class="container">
    <div class="pb-3">
        <form method="get" action="/admin/booking_calender">
            <div class="row">
                <div class="col-lg-4">
                    <div class="input-group">
                        <select name="roomtype" id="roomtype" class="custom-select form-control" >
                            @foreach($rooms as $room)
                            <option value="{{$room->id}}" @if(request()->roomtype==$room->id) selected
                                @endif>{{$room->roomtype->name}} - {{$room->bedtype->name}} </option>
                            @endforeach
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit">Check</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div id="calendar1"></div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.js"></script>
<script>
    $(document).ready(function () {

            $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            });

            var roomtype =document.getElementById("roomtype").value;;
            var calendar = $('#calendar1').fullCalendar({
                editable: true,
                // events: "{{url('/')}}/admin/booking_calendar?roomtype={{request()->roomtype}}",
                // displayEventTime: false,
                editable: true,
                showNonCurrentDates: true,
                selectable: true,
                selectHelper: true,

                select: function(start, end, allDay) {
                      var startFix= moment($.fullCalendar.formatDate(start, 'YYYY-MM-DD'));
                      var date=startFix._i;

                    $.ajax({
                        url: `/admin/available-room-qty?roomtype=${roomtype}&date=${date}`,
                        type: 'GET',
                        success: function(res) {
                            if(res.result){
                            var data = res.data;
                            var tbody = '';
                            data.forEach(function(item){
                                tbody += `<tr>
                                    <td class="text-center">${item.room_type}</td>
                                    <td class="text-center">${item.bed_type}</td>
                                    <td class="text-center">${item.qty}</td>
                                    </tr>`;
                            });

                            Swal.fire({
                                title: '<strong>Available Room </strong>',
                                icon: 'info',
                                html:
                                `<div class="table-responsive">
                                <table class="table table-bordered">
                                <thead>
                                <th class="text-center">Room Type</th>
                                <th class="text-center">Bed Type</th>
                                <th class="text-center">Qty</th>
                                </thead>
                                <tbody>
                                ${tbody}
                                </tbody>
                                </table>
                                </div>`
                            });
                            }else{
                                alert('Available Room Searching Error!');
                            }

                        }
                    });
                },

                // eventRender: function (event, element, view) {
                //     if (event.allDay === 'true') {
                //             event.allDay = true;
                //     } else {
                //             event.allDay = false;
                //     }
                // },

                dayRender: function(res,cell) {

                var roomtype =document.getElementById("roomtype").value;;
                var day=res._d.getDate();
                var month=res._d.getMonth()+1;
                var year=res._d.getFullYear();
                var fulldate=year+'-0'+month+'-'+day;

                if(day < 10 ){
                    var fulldate=year+'-0'+month+'-'+'0'+day;
                }
                    $.ajax({
                            url: `/admin/available-room-qty?roomtype=${roomtype}&date=${fulldate}`,
                            type: 'GET',
                            success: function(info) {
                                if(info.result){
                                var data = info.data;
                                var tbody = '';
                                data.forEach(function(item){
                                    tbody += `
                                        <tr>
                                        <td class="text-center qty"><h6>${item.qty} - Left</h6 >  </td></br>
                                        <td class="text-center"><b>${item.price}</b>  MMK </td></br>
                                        <td class="text-center"><b>${item.foreign_price}</b> USD </td>
                                        </tr>
                                        `;
                                });
                                cell.append(`<div style="text-align:center; background-color:#fff;padding:2px 0;margin-top:20px;"> ${tbody} </br>  </div>`);
                                }
                            }
                    });
                },

                eventClick: function(info) {
                    // Swal.fire({
                    //     title: '<strong>Available Room </strong>',
                    //     icon: 'info',
                    //     html:
                    // `<div class="table-responsive">
                    // <table class="table table-bordered">
                    // <tbody>
                    // <tr><td>Available Room Qty </td><td>${info.availiable_room_qty}</td></tr>
                    // <tr><td colspan="2" class="bg-light"></td></tr>
                    // <tr><td colspan="2"><h2 class="text-center">Booking Information</h2></td></tr>
                    // <tr><td>Booking Number</td><td>${info.booking_number}</td></tr>
                    // <tr><td>Nationality</td><td>${info.nationality[info.national]}</td></tr>
                    // <tr><td>Room Type</td><td>${info.room_type}</td></tr>
                    // <tr><td>Checkin - Checkout</td><td>${info.start._i} - ${info.end._i}</td></tr>
                    // <tr><td>Room Qty</td><td>${info.room_qty} Room</td></tr>

                    // <tr><td>Guest</td><td>${info.guest} Person</td></tr>
                    // <tr><td>Early Check-In Time </td><td> ${info.early_checkin_time} </td></tr>
                    // <tr><td>Late Check-Out Time </td><td> ${info.late_checkout_time} </td></tr>
                    // <tr><td>Other Service Total Charges</td><td>${info.sign1} ${info.other_charges_total} ${info.sign2}</td></tr>

                    // <tr><td colspan="2" class="bg-light"></td></tr>
                    // <tr><td>Price (per night)</td><td>${info.sign1} ${info.price} ${info.sign2}</td></tr>
                    // <tr><td>Extra Bed Qty </td><td> ${info.extra_bed_qty} </td></tr>
                    // <tr><td>Early Check-In Price </td><td> ${info.early_check_in} </td></tr>
                    // <tr><td>Late Check-Out Price </td><td> ${info.late_check_out} </td></tr>
                    // <tr><td>Both Early/Late Check Price </td><td> ${info.both_check} </td></tr>

                    // <tr><td>Extra Bed Total Price </td><td> ${info.extra_bed_total} </td></tr>
                    // <tr><td>Service Charges</td><td>${info.sign1} ${info.service_tax} ${info.sign2}</td></tr>

                    // <tr><td>Total</td><td>${info.sign1} ${info.total} ${info.sign2}</td></tr>
                    // <tr><td>Commercial Taxes</td><td>${info.sign1} ${info.commercial_tax} ${info.sign2}</td></tr>


                    // <tr><td>Grand Total</td><td>${info.sign1} ${info.grand_total} ${info.sign2}</td></tr>
                    // <tr><td colspan="2" class="bg-light"></td></tr>
                    // <tr><td>Name</td><td>${info.name}</td></tr>
                    // <tr><td>Email</td><td>${info.email}</td></tr>
                    // <tr><td>Phone</td><td>${info.phone}</td></tr>
                    // </tbody>
                    // </table>
                    // </div>`
                    // });
                }
            });
    });

    function displayMessage(message) {
        $(".response").html("<div class='success'>"+message+"</div>");
        setInterval(function() { $(".success").fadeOut(); }, 1000);
    }
</script>
@endsection
