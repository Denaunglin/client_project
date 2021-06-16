<link rel="stylesheet" href="{{ asset('vendor/assets/css/main.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/assets/css/lib.css') }}">
<link rel="stylesheet" href="{{ asset('fonts/vendor/fontawesome/css/all.min.css') }}">
{{-- <link href='https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/dropzone.css' rel='stylesheet'> --}}
<link rel="stylesheet" href="{{asset('assets/dropzone/dropzone.css')}}">

<!-- Datatable -->
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css"> --}}
​<link rel="stylesheet" href="{{asset('assets/dataTable/datatable.css')}}">

<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css">

{{-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.dataTables.min.css"> --}}
<link rel="stylesheet" href="{{asset('assets/dataTable/button_datatables.css')}}">
{{-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.bootstrap4.min.css"> --}}
<link rel="stylesheet" href="{{asset('assets/dataTable/button_datatables.css')}}">

{{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.18.0/css/mdb.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet"> --}}

{{-- Daterange Picker --}}
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> --}}
<link rel="stylesheet" href="{{asset('assets/daterangepicker/daterangepicker.css')}}">

{{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css" rel="stylesheet" /> --}}
<link rel="stylesheet" href="{{asset('assets/select2/select2.css')}}">

{{-- <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css"
    integrity="sha256-nbyata2PJRjImhByQzik2ot6gSHSU4Cqdz5bNYL2zcU=" crossorigin="anonymous" /> --}}
<link rel="stylesheet" href="{{asset('assets/select2/select2_bootstrap.css')}}" integrity="sha256-nbyata2PJRjImhByQzik2ot6gSHSU4Cqdz5bNYL2zcU=" crossorigin="anonymous" />

{{-- <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.4.0/css/perfect-scrollbar.min.css"
    integrity="sha256-Eff0vTAskMNGMXDva8NMruf8ex6k9EuZ4QXf09lxwaQ=" crossorigin="anonymous" /> --}}
<link rel="stylesheet" href="{{asset('assets/scrollbar/scrollbar.css')}}" integrity="sha256-Eff0vTAskMNGMXDva8NMruf8ex6k9EuZ4QXf09lxwaQ=" crossorigin="anonymous" />


{{-- Image Viewer --}}
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.3.6/viewer.css"> --}}
<link rel="stylesheet" href="{{asset('assets/imageviewer/viewer.css')}}">

<style>
    .app-page-title {
        padding: 15px 30px !important;
        margin: -30px -30px 0px !important;
    }

    th.action {
        width: 100px !important;
    }

    .action a {
        cursor: pointer;
        display: inline-block;
        padding: 5px;
    }

    .action a i {
        padding: 0;
        font-size: 18px !important;
    }

    .action a:hover {
        background: #f5f5f5;
    }

    /*------------- Sweetalert 2 --------------  */
    .confirm-box {
        padding-bottom: 5px !important;
    }

    .confirm-box .swal2-header .swal2-title {
        font-size: 20px !important;
    }

    .confirm-box .swal2-content .swal2-select,
    .confirm-box .swal2-content .swal2-input {
        width: 100% !important;
        color: #545454 !important;
        border: 1px solid #ddd !important;
        margin: 5px auto !important;
        border-radius: 5px !important;
        font-size: 16px !important;
        height: 36px !important;
        padding: 3px !important;
    }

    .confirm-box .swal2-actions {
        margin: 30px auto 0 !important;
    }

    .confirm-box .swal2-actions .swal2-confirm,
    .confirm-box .swal2-actions .swal2-cancel {
        transform: scale(0.9);
        border-radius: 5px !important;
        padding: 10px 24px !important;
    }

    .confirm-box .swal2-actions::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 1px;
        background: #ddd;
        top: 60%;
    }

    .danger-bg .swal-footer .swal-button-container .swal-button--confirm {
        background-color: #e64942 !important;
    }

    .success-bg .swal-footer .swal-button-container .swal-button--confirm {
        background-color: #20c997 !important;
    }

    .info-bg .swal-footer .swal-button-container .swal-button--confirm {
        background-color: #a2a9b1 !important;
    }

    .swal-overlay {
        background-color: rgba(17, 20, 24, 0.42);
    }

    .swal-footer {
        /* background-color: rgb(245, 248, 250); */
        margin-top: 32px;
        border-top: 1px solid #E9EEF1;
        overflow: hidden;
    }

    .choose-modal {
        width: 600px !important;
    }

    .choose-modal.swal-modal .swal-icon--info {
        margin-top: 15px !important;
    }

    .choose-modal.swal-modal .swal-title {
        font-size: 16px;
    }

    .choose-modal.swal-modal .swal-footer {
        margin-top: 20px;
    }

    .choose-modal.swal-modal .swal-button--default {
        background: #a2a9b1 !important;
    }

    .choose-modal.swal-modal .swal-button--reject {
        background: #e64942 !important;
    }

    .choose-modal.swal-modal .swal-button--approve {
        background: #444 !important;
    }

    .choose-modal.swal-modal .swal-button--issue {
        background: orange !important;
    }

    .choose-modal.swal-modal .swal-button--confirm {
        background: #20c997 !important;
    }

    .status-modal .swal2-modal {
        padding: 0 !important;
    }

    .status-modal .swal2-header {
        margin: 20px auto;
    }

    .status-modal .swal2-header .swal2-title {
        font-size: 18px;
    }

    .status-modal .swal2-content {
        text-align: left !important;
        font-size: 16px !important;
        padding: 15px;
    }

    .status-modal .swal2-actions {
        border-top: 1px solid #ddd;
        justify-content: flex-end !important;
        margin: 30px auto 0 !important;
        padding: 10px 15px;
    }

    .status-modal .swal2-actions .swal2-cancel {
        background: #a2a9b1 !important;
        padding: 8px 30px !important;
    }

    .status-modal .swal2-actions .swal2-confirm {
        background: #20c997 !important;
        padding: 8px 30px !important;
    }

    .detail-modal .swal2-modal {
        padding: 0 !important;
    }

    .detail-modal .swal2-header {
        margin: 5px auto;
    }

    .detail-modal .swal2-header .swal2-title {
        font-size: 18px;
    }

    .detail-modal .swal2-content {
        text-align: left !important;
        font-size: 16px !important;
        padding: 15px;
    }

    .confirm-modal .swal2-title {
        font-size: 24px !important;
    }

    .confirm-modal .swal2-styled.swal2-cancel {
        padding: 8px 30px !important;
    }

    .confirm-modal .swal2-styled.swal2-confirm {
        padding: 8px 30px !important;
    }

    .schedule-modal.swal-modal .swal-icon--info {
        margin-top: 15px !important;
    }

    .schedule-modal.swal-modal .swal-footer {
        margin-top: 20px;
    }

    .schedule-modal.swal-modal .swal-button--default {
        background: #a2a9b1 !important;
    }

    .schedule-modal.swal-modal .swal-button--existing {
        background: black !important;
    }

    .schedule-modal.swal-modal .swal-button--new {
        background: #20c997 !important;
    }

    .off-day-box {
        padding-bottom: 5px !important;
    }

    .off-day-box .swal2-header .swal2-title {
        font-size: 20px !important;
    }

    .off-day-box label {
        font-size: 16px !important;
    }

    .off-day-box .swal2-actions {
        margin: 0 auto !important;
    }

    .off-day-box .swal2-actions .swal2-confirm,
    .off-day-box .swal2-actions .swal2-cancel {
        -webkit-transform: scale(0.9);
        transform: scale(0.9);
        padding: 10px 24px !important;
    }

    /* -------------------- Datatable ------------------  */
    div.dt-button-background {
        background: transparent !important;
        z-index: 0 !important;
    }

    div.dt-button-collection {
        min-width: 120px;
        width: 120px;
    }

    .buttons-page-length {
        background: #fff;
        color: #333;
    }

    .buttons-page-length:hover {
        background: #fff;
        color: #333;
    }

    .dt-button-collection.dropdown-menu .dt-button.dropdown-item {
        background: #fff !important;
        box-shadow: none !important;
        border: none !important;
        padding: 3px 5px;
    }

    .dt-button-collection.dropdown-menu .dt-button.dropdown-item:hover {
        background: #f5f5f5 !important;
        color: #2955C8 !important;
        box-shadow: none !important;
    }

    .dt-button-collection.dropdown-menu .dt-button.dropdown-item.active {
        background: #f5f5f5 !important;
        color: #2955C8 !important;
        box-shadow: none !important;
    }

    div.dataTables_wrapper div.dataTables_filter {
        float: right;
        margin-right: 15px;
    }

    div.dataTables_wrapper div.dataTables_length {
        margin-left: 15px;
    }

    div.dataTables_wrapper div.dataTables_info {
        margin-left: 15px;
    }

    div.dataTables_wrapper div.dataTables_paginate {
        margin-right: 15px;
    }

    .dataTables_wrapper table {
        border-bottom: 1px solid #ddd;
    }

    .dataTables_wrapper thead {
        background: #fcfcfc !important;
    }

    .dataTables_wrapper thead th {
        border-bottom-width: 0px !important;
    }

    .dataTables_wrapper tr td:nth-of-type(1) {
        width: 140px !important;
    }

    .dataTables_wrapper .list-group-item {
        padding: 5px !important;
    }

    tr.selected {
        background: #bbb;
    }

    div.dataTables_wrapper div.dataTables_processing {
        position: absolute;
        top: 12%;
        left: 40%;
        width: 200px;
        margin-left: 0;
        margin-top: 0;
        text-align: center;
        padding: 1em 0;
        z-index: 999;
    }

    div.dataTables_wrapper div.dataTables_processing p {
        margin-bottom: 0 !important;
    }

    div.dataTables_wrapper div.dataTables_processing img {
        width: 40px !important;
    }

    div.dataTables_wrapper div.dataTables_processing[style="display: block;"]~.dataTable tbody,
    div.dataTables_wrapper div.dataTables_processing[style="display: block;"]~.dataTables_scroll tbody {
        opacity: 0.4;
        position: relative;
        z-index: -1;
    }

    /* ------------------------ Img review -------------- */
    div[class$="image_preview"] img {
        padding: 5px;
        box-shadow: 0px 0px 4px -2px black;
        margin-top: 10px;
        margin-right: 7px;
        margin-bottom: 10px;
        height: 80px;
    }
</style>
@yield('style')
