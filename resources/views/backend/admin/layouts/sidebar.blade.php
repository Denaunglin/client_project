<div class="app-sidebar sidebar-shadow">
    <div class="app-header__logo">

        <style>
            .logo-src::before {
                content: 'Apex Hotel ';
                background-color: #2b3d43;
                font-weight: 500;
                font-size: 1.2rem;
                color: white;
                width: 100%;
                height: 100px;
            }
        </style>

        <div class="logo-src"></div>
        <div class="header__pane ml-auto">
            <div>
                <button type="button" class="hamburger close-sidebar-btn hamburger--elastic"
                    data-class="closed-sidebar">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>

    <div class="app-header__mobile-menu">
        <div>
            <button type="button" class="hamburger hamburger--elastic mobile-toggle-nav">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </button>
        </div>
    </div>

    <div class="app-header__menu">
        <span>
            <button type="button" class="btn-icon btn-icon-only btn btn-primary btn-sm mobile-toggle-header-nav">
                <span class="btn-icon-wrapper">
                    <i class="fa fa-ellipsis-v fa-w-6"></i>
                </span>
            </button>
        </span>
    </div>

    <div class="scrollbar-sidebar">
        <div class="app-sidebar__inner">
            <ul class="vertical-nav-menu">

                <li class="app-sidebar__heading">@lang('message.header.main')</li>
                <li>
                    <a href="{{route('admin.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-home"></i>
                        @lang('message.header.dashboard')
                    </a>
                </li>
                <li>
                    <a href="{{route('admin.shop_storages.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-home"></i>
                        Shop Storage
                    </a>
                </li>
               
{{--                 
                @can('view_booking')
                <li class="app-sidebar__heading">Item Management</li>
                <li>
                    <a href="{{route('admin.booking.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-bookmarks"></i>
                        Item Category
                        @if(count($newBookings))
                        <span class="badge badge-pill badge-success">{{count($newBookings)}}</span>
                        @endif
                    </a>
                </li>
                @endcan

                @can('view_booking_calendar')
                <li>
                    <a href="{{route('admin.booking_calender.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-date"></i>
                        Item Sub Category
                    </a>
                </li>
                @endcan
                @can('view_invoice')
                <li>
                    <a href="{{route('admin.invoices.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-file"></i>
                        Item & Prices
                    </a>
                </li>
                @endcan
                @can('view_extra_invoice')
                <li>
                    <a href="{{route('admin.extrainvoices.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-file"></i>
                        Extra Invoice History
                    </a>
                </li>
                @endcan

                 @can('view_payslip')
                <li>
                    <a href="{{route('admin.payslips.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-news-paper"></i>
                        PaySlip
                        @if(count($newPayslip))
                        <span class="badge badge-pill badge-success">{{count($newPayslip)}}</span>
                        @endif
                    </a>
                </li>
                @endcan --}}
                
                @can('view_room_plan')
                <li class="app-sidebar__heading">@lang("message.header.item_management")</li>
                @endcan
                @can('view_item_category')
                <li>
                    <a href="{{route('admin.item_categories.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-box1"></i>
                        @lang("message.header.item_category")
                    </a>
                </li>
                @endcan
                @can('view_item_sub_category')
                <li>
                    <a href="{{route('admin.item_sub_categories.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-box1"></i>
                        @lang("message.header.item_sub_category")
                    </a>
                </li>
                @endcan
                @can('view_item')
                <li>
                    <a href="{{route('admin.items.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-box1"></i>
                        @lang("message.header.item_price")
                    </a>
                </li>
                @endcan
                @can('view_item')
                <li>
                    <a href="{{route('admin.remain_items.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-box1"></i>
                        @lang("message.header.remaining_item_list")
                    </a>
                </li>
                @endcan
                {{-- @can('view_item')
                <li>
                    <a href="{{route('admin.reorder_list')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-box1"></i>
                        @lang("message.header.reorder_list")
                    </a>
                </li>
                @endcan --}}
              

                @can('view_room_plan')
                <li class="app-sidebar__heading">@lang("message.header.commodity_sales")</li>
                @endcan
                @can('view_item_category')
                <li>
                    <a href="{{route('admin.sell_items.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        @lang("message.header.commodity_sales")
                    </a>
                </li>
                @endcan
                @can('view_item_sub_category')
                <li>
                    <a href="{{route('admin.daily_sales.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        @lang("message.header.daily_sales")
                    </a>
                </li>
                @endcan
              
                @can('view_room_plan')
                <li class="app-sidebar__heading">@lang("message.header.merchandise")</li>
                @endcan
                @can('view_item_category')
                <li>
                    <a href="{{route('admin.buying_items.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        @lang("message.header.merchandise")
                    </a>
                </li>
                @endcan
                @can('view_item_sub_category')
                <li>
                    <a href="{{route('admin.item_sub_categories.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        @lang("message.header.order_list")
                    </a>
                </li>
                @endcan
                @can('view_item')
                <li>
                    <a href="{{route('admin.items.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        @lang("message.header.item_price")
                    </a>
                </li>
                @endcan

                 @can('view_room_plan')
                <li class="app-sidebar__heading">@lang("message.header.ledger")</li>
                @endcan
                @can('view_item_category')
                <li>
                    <a href="{{route('admin.item_ledgers.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        @lang("message.header.ledger")
                    </a>
                </li>
                @endcan
                @can('view_item_category')
                <li>
                    <a href="{{route('admin.item_categories.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        @lang("message.header.estimate_profit")
                    </a>
                </li>
                @endcan
                @can('view_item_sub_category')
                <li>
                    <a href="{{route('admin.item_sub_categories.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        @lang("message.header.daily_sales")
                    </a>
                </li>
                @endcan
                @can('view_item')
                <li>
                    <a href="{{route('admin.items.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        @lang("message.header.commodity_recieve")
                    </a>
                </li>
                @endcan

                @can('view_room_plan')
                <li class="app-sidebar__heading">@lang("message.header.expense")</li>
                @endcan
                @can('view_item_category')
                <li>
                    <a href="{{route('admin.expenses.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        @lang("message.header.expense")
                    </a>
                </li>
                @endcan
                @can('view_item_sub_category')
                <li>
                    <a href="{{route('admin.expense_categories.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        @lang("message.header.expense_category")
                    </a>
                </li>
                @endcan
                @can('view_item')
                <li>
                    <a href="{{route('admin.expense_types.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        @lang("message.header.expense_type")
                    </a>
                </li>
                @endcan

                @can('view_room_plan')
                <li class="app-sidebar__heading">@lang("message.header.report")</li>
                @endcan
                @can('view_item_category')
                <li>
                    <a href="{{route('admin.item_categories.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        @lang("message.header.commodity_daily_report")
                    </a>
                </li>
                @endcan
                @can('view_item')
                <li>
                    <a href="{{route('admin.order_lists.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-box1"></i>
                        @lang("message.header.order_list")
                    </a>
                </li>
                @endcan
                @can('view_item_sub_category')
                <li>
                    <a href="{{route('admin.item_sub_categories.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        @lang("message.header.daily_sales")
                    </a>
                </li>
                @endcan
                @can('view_item_sub_category')
                <li>
                    <a href="{{route('admin.services.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        @lang("message.header.services")
                    </a>
                </li>
                @endcan
                {{-- @can('view_room_layout')
                <li>
                    <a href="{{route('admin.roomlayouts.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        Room Layout
                    </a>
                </li>
                @endcan --}}

                {{-- @can('view_room_schedule')
                <li>
                    <a href="{{route('admin.roomschedules.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-stopwatch"></i>
                        Room Schedule
                    </a>
                </li>
                @endcan
                @can('view_room_plan')
                <li>
                    <a href="{{url('admin/roomplan')}}?date={{date('Y-m-d')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-culture"></i>
                        Room Plan
                    </a>
                </li>
                @endcan --}}

                {{-- @can('view_message')
                <li class="app-sidebar__heading">Contact Message</li>
                <li>
                    <a href="{{route('admin.messages.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-mail-open"></i>
                        Messages
                        @if(count($newMessages))
                        <span class="badge badge-pill badge-success">{{count($newMessages)}}</span>
                        @endif
                    </a>
                </li>
                @endcan --}}

                {{-- @can('view_sendNotification')
                <li class="app-sidebar__heading">Notification Management</li>
                <li>
                    <a href="{{route('admin.sendnotifications.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-bell"></i>
                        Send Notifications
                    </a>
                </li>
                @endcan --}}

                  {{-- @can('view_message')
                <li class="app-sidebar__heading">@lang("message.header.activity_log")</li>
                <li>
                    <a href="{{route('admin.activity_log.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-note2"></i>
                        @lang("message.header.activity_log")                  
                    </a>
                </li>
                @endcan

                @can('view_other_service_category')
                <li class="app-sidebar__heading">Other Service Management</li>
                <li>
                    <a href="{{route('admin.otherservicescategory.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-albums"></i>
                        Other Services Category
                    </a>
                </li>
                @endcan
                @can('view_other_service_item')
                <li>
                    <a href="{{route('admin.otherservicesitem.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-albums"></i>
                        Other Services Item
                    </a>
                </li>
                @endcan --}}

                @can('view_discount')
                <li class="app-sidebar__heading">Bussiness Info</li>
                <li>
                    <a href="{{route('admin.bussiness_infos.index')}}" class="sidebar-item">
                        <i class="metismenu-icon  pe-7s-ticket "></i>
                        Bussiness Info
                    </a>
                </li>
                @endcan

                @can('view_discount')
                <li class="app-sidebar__heading">Price Management</li>
                <li>
                    <a href="{{route('admin.discounts.index')}}" class="sidebar-item">
                        <i class="metismenu-icon  pe-7s-ticket "></i>
                        Discounts & Addon
                    </a>
                </li>
                @endcan

                {{-- @can('view_extra_bed_price')
                <li>
                    <a href="{{route('admin.extrabedprices.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-plus"></i>
                        Extra Bed Prices
                    </a>
                </li>
                @endcan --}}

                   {{-- @can('view_earlylatecheck')
                <li>
                    <a href="{{route('admin.earlylatechecks.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-clock"></i>
                        Early / Late-Check Prices
                    </a>
                </li>
                @endcan --}}

                @can('view_user')
                 <li class="app-sidebar__heading">User Management</li>
                <li>
                    <a href="{{route('admin.client-users.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-users"></i>
                         @lang("message.header.customer")   
                    </a>
                </li>
                @endcan
                
                @can('view_user')
               <li>
                   <a href="{{route('admin.suppliers.index')}}" class="sidebar-item">
                       <i class="metismenu-icon pe-7s-users"></i>
                        @lang("message.header.supplier")   
                   </a>
               </li>
               @endcan
                    {{-- @can('view_account_type')
                    <li>
                        <a href="{{route('admin.accounttypes.index')}}" class="sidebar-item">
                            <i class="metismenu-icon pe-7s-id"></i>
                            User Account Type
                        </a>
                    </li>
                    @endcan

                    @can('view_user_nrc_image')
                    <li>
                        <a href="{{route('admin.usernrcimages.index')}}" class="sidebar-item">
                            <i class="metismenu-icon pe-7s-photo"></i>
                            User Nrc Image
                        </a>
                    </li>
                    @endcan

                    @can('view_user_credit')
                    <li>
                        <a href="{{route('admin.usercreditcards.index')}}" class="sidebar-item">
                            <i class="metismenu-icon pe-7s-credit"></i>
                            User Credit Cards
                        </a>
                    </li>
                    @endcan

                    @can('view_payment_card')
                    <li>
                        <a href="{{route('admin.cardtypes.index')}}" class="sidebar-item">
                            <i class="metismenu-icon pe-7s-cash"></i>
                            Payment Card Type
                        </a>
                    </li>
                    @endcan --}}

                @can('view_tax')
                <li class="app-sidebar__heading">Setting</li>
                <li>
                    <a href="{{route('admin.taxes.index')}}" class="sidebar-item">
                        <i class="metismenu-icon  pe-7s-eyedropper "></i>
                        Taxes
                    </a>
                </li>
                @endcan
                {{-- @can('view_slider')
                <li>
                    <a href="{{route('admin.slider_upload.index')}}" class="sidebar-item">
                        <i class="metismenu-icon  pe-7s-photo "></i>
                        Slider
                    </a>
                </li>
                @endcan --}}
                @can('view_checkin_deposit')
                <li>
                    <a href="{{route('admin.deposits.index')}}" class="sidebar-item">
                        <i class="metismenu-icon  pe-7s-piggy "></i>
                        Check-In Deposit
                    </a>
                </li>
                @endcan

                {{-- @can('view roles')
                <li>
                    <a href="{{route('admin.roles.index')}}?guard={{config('custom_guards.default.user')}}"
                class="sidebar-item">
                <i class="metismenu-icon pe-7s-helm"></i>
                User Roles
                </a>
                </li>
                @endcan --}}

                @can('view_admin')
                <li class="app-sidebar__heading">Admin User Management</li>
                <li>
                    <a href="{{route('admin.admin-users.index')}}" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-users"></i>
                        Admin Users
                    </a>
                </li>
                @endcan
                @can('view_admin_user_roles')
                <li>
                    <a href="{{route('admin.roles.index')}}?guard={{config('custom_guards.default.admin')}}"
                        class="sidebar-item">
                        <i class="metismenu-icon pe-7s-helm"></i>
                        Admin Users Roles
                    </a>
                </li>
                @endcan
                @can('view_permission')
                <li class="app-sidebar__heading">Permission Management</li>
                <li>
                    <a href="{{route('admin.permission-group.index')}}?guard=admin" class="sidebar-item">
                        <i class="metismenu-icon pe-7s-door-lock"></i>
                        Permissions
                    </a>
                </li>
                @endcan
            </ul>
        </div>
    </div>
</div>
