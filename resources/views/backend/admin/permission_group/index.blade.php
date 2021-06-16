@extends('backend.admin.layouts.app')
@section('title','All Permission Group')

@php
if(request('guard') == 'admin') {
$name = 'Admin';
} else {
$name = 'Customer';
}
@endphp

@section('meta_title', 'All '.$name.' Permission Group')
@section('page_title', 'All '.$name.' Permission Group')
@section('page_title_icon')
<i class="metismenu-icon pe-7s-lock"></i>
@endsection

@section('extra_css')
<style>
    .list-group i {
        font-size: 18px;
    }

    th.action {
        width: 80px !important;
    }
</style>
@endsection

@section('page_title_buttons')
<div class="d-flex justify-content-end">
    @if( Gate::check('add_permission') || Gate::check('add_terminal_permission') ||
    Gate::check('add_customer_permission') )
    <a href="{{ route('admin.permission-group.create') }}?guard={{request('guard')}}" class="btn btn-primary">Add
        Permission Group</a>
    @endif
</div>
@endsection

@section('content')
<section class="content pb-3">
    <div class="row">
        <div class="col-lg-12">
            <div class="card tablePage">
                <div class="card-body">
                    <table class="table table-bordered PermissionGroup_tb" style="width:100%;">
                        <thead>
                            <tr class="bg-light-blue">
                                <th></th>
                                <th>Rank</th>
                                <th>Name</th>
                                <th class="no-sort">Permissions</th>
                                <th class="no-sort action">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
    $(document).ready(function() {
    var table = $(".PermissionGroup_tb").DataTable({
      processing: true,
      serverSide: true,
      ajax: "/admin/permission-group?guard={{request('guard')}}",
      columns: [{
          data: "plus-icon",
          name: "plus-icon",
          defaultContent: null
        },
        {
          data: "rank",
          name: "rank",
          defaultContent: "-",
          class: ""
        },
        {
          data: "name",
          name: "name",
          defaultContent: "-",
          class: ""
        },
        {
          data: "permissions",
          name: "permissions",
          defaultContent: "-",
          class: ""
        },
        {
          data: "action",
          name: "action",
          class: ""
        }
      ],
      order: [
        [1, "asc"]
      ],
      responsive: {
        details: {
          type: "column",
          target: 0
        }
      },
      columnDefs: [{
          targets: "no-sort",
          orderable: false
        },
        {
          className: "control",
          orderable: false,
          targets: 0
        },
        {
          targets: "hidden",
          visible: false
        }
      ],
        pagingType: "simple_numbers",
        language: {
            paginate: {previous: "«", next: "»"},
            processing: `<div class="processing_data">
        <div class="spinner-border text-info" role="status">
            <span class="sr-only">Loading...</span>
        </div></div>`
        }
    });

    $(document).on('click', '.delete', function(e) {
      e.preventDefault();
      var id = $(this).data('id');
      swal("Are you sure, you want to delete?", {
          className: "danger-bg",
          buttons: [true, "Yes"],
        })
        .then((willDelete) => {
          if (willDelete) {
            $.ajax({
              url: `/admin/permission-group/${id}?guard={{request('guard')}}`,
              type: 'DELETE',
              success: function() {
                table.ajax.reload();
              }
            });
          }
        });
    });

    $(document).on('click', '.permission-delete', function(e) {
      e.preventDefault();
      var id = $(this).data('id');
      swal("Are you sure, you want to delete?", {
          className: "danger-bg",
          buttons: [true, "Yes"],
        })
        .then((willDelete) => {
          if (willDelete) {
            $.ajax({
              url: `/admin/permission/${id}?guard={{request('guard')}}`,
              type: 'DELETE',
              success: function() {
                table.ajax.reload();
              }
            });
          }
        });
    });
  });
</script>
@endsection
