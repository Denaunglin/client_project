<div class="app-sidebar sidebar-shadow">
    <div class="app-header__logo">

        <style>
            .logo-src::before {
                content: '';
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
                    <a href="{{route('admin.sell_items.index')}}"  class="menu-list-member  @yield('dashboard-active')">
                        <i class="fa fa-home mr-3" style="font-size: 20px" ></i>
                        @lang('message.header.dashboard')
                    </a>
                </li>
                <hr>
                <li>
                    <a href="{{route('admin.shop_storages.index')}}"  class="menu-list-member  @yield('shop-storage-active')">
                        <i class="fa fa-store mr-3" style="font-size: 20px" ></i>
                        @lang('message.header.shop_storage')
                    </a>
                </li>
                <hr>
                <li>
                    <a href="{{route('admin.cash_books.index')}}"  class="menu-list-member  @yield('cash-book-active')">
                        <i class="fa fa-cash-register mr-3" style="font-size: 20px" ></i>
                        @lang('message.header.cash_book')

                    </a>
                </li>
               <hr>
              
            
                {{-- @can('view_payslip')
                <li>
                    <a href="{{route('admin.payslips.index')}}"  class="menu-list-member  @yield('payslip-active')">
                        <i class="metismenu-icon pe-7s-news-paper"></i>
                        @lang('message.header.payslip')
                        @if(count($newPayslip))
                        <span class="badge badge-pill badge-success">{{count($newPayslip)}}</span>
                        @endif
                    </a>
                </li>
                @endcan --}}
               
                @can('view_item_sub_category')
                <li>
                    <a href="{{route('admin.services.index')}}"  class="menu-list-member  @yield('service-active')">
                        <i class="fa fa-wrench mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.services")
                    </a>
                </li>
                @endcan
                <hr>
                @can('view_invoice')
                <li>
                    <a href="{{route('admin.invoices.index')}}"  class="menu-list-member  @yield('invoice-active')">
                        <i class="fa fa-file-invoice mr-3" style="font-size: 20px" ></i>
                        @lang('message.invoice')
                    </a>
                </li>
                @endcan
                <hr>

                @can('view_room_plan')
                <li class="app-sidebar__heading">@lang("message.header.merchandise")</li>
                @endcan
                @can('view_item_category')
                <li>
                    <a href="{{route('admin.buying_items.index')}}"  class="menu-list-member  @yield('merchandise-active')">
                        <i class="fa fa-archive mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.merchandise")
                    </a>
                </li>
                @endcan
                <hr>

                 
                {{-- @can('view_room_plan')
                <li class="app-sidebar__heading">@lang("message.header.merchandise")</li>
                @endcan
                @can('view_item_category')
                <li>
                    <a href="{{route('admin.buying_items.index')}}"  class="menu-list-member  @yield('merchandise-active')">
                        <i class="fa fa-archive mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.merchandise")
                    </a>
                </li>
                @endcan
                <hr> --}}
                @can('view_room_plan')
                <li class="app-sidebar__heading">@lang("message.header.commodity_sales")</li>
                @endcan
                @can('view_item_category')
                <li>
                    <a href="{{route('admin.sell_items.index')}}"  class="menu-list-member  @yield('commodity-sale-active')">
                        <i class="fa fa-archive mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.commodity_sales")
                    </a>
                </li>
                @endcan
              <hr>

                @can('view_room_plan')
                <li class="app-sidebar__heading">@lang("message.header.item_management")</li>
                @endcan
                @can('view_item_category')
                <li>
                    <a href="{{route('admin.item_categories.index')}}"  class="menu-list-member  @yield('item-category-active')">
                        <i class="fa fa-archive mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.item_category")
                    </a>
                </li>
                @endcan
                <hr>
                @can('view_item_sub_category')
                <li>
                    <a href="{{route('admin.item_sub_categories.index')}}"  class="menu-list-member  @yield('item-sub-category-active')">
                        <i class="fa fa-archive mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.item_sub_category")
                    </a>
                </li>
                @endcan
                <hr>
                @can('view_item')
                <li>
                    <a href="{{route('admin.items.index')}}"  class="menu-list-member  @yield('item-price-active')">
                        <i class="fa fa-archive mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.item_price")
                    </a>
                </li>
                @endcan
                <hr>
                {{-- @can('view_item')
                <li>
                    <a href="{{route('admin.opening_items.index')}}"  class="menu-list-member  @yield('opening-item-active')">
                        <i class="fa fa-archive mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.opening_item")
                    </a>
                </li>
                @endcan
                <hr> --}}
                <li>
                    <a href="{{route('admin.return_items.index')}}"  class="menu-list-member  @yield('return-item-active')">
                        <i class="fa fa-archive mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.return_item")
                    </a>
                </li>
                <hr>
             
                 @can('view_room_plan')
                <li class="app-sidebar__heading">@lang("message.header.ledger")</li>
                @endcan
                @can('view_item_category')
                <li>
                    <a href="{{route('admin.item_ledgers.index')}}"  class="menu-list-member  @yield('ledger-active')">
                        <i class="fa fa-file mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.ledger")
                    </a>
                </li>
                @endcan
                <hr>
                @can('view_item_category')
                <li>
                    <a href="{{route('admin.profit_lost.index')}}"  class="menu-list-member  @yield('estimate-profit-active')">
                        <i class="fa fa-chart-area mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.estimate_profit")
                    </a>
                </li>
                @endcan
                <hr>
                @can('view_room_plan')
                <li class="app-sidebar__heading">@lang("message.header.expense")</li>
                @endcan
                @can('view_item_category')
                <li>
                    <a href="{{route('admin.expenses.index')}}"  class="menu-list-member  @yield('expense-active')">
                        <i class="fa fa-tag mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.expense")
                    </a>
                </li>
                @endcan
                <hr>
                @can('view_item_sub_category')
                <li>
                    <a href="{{route('admin.expense_categories.index')}}"  class="menu-list-member  @yield('expense-category-active')">
                        <i class="fa fa-tag mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.expense_category")
                    </a>
                </li>
                @endcan
                <hr>
                @can('view_item')
                <li>
                    <a href="{{route('admin.expense_types.index')}}"  class="menu-list-member  @yield('expense-type-active')">
                        <i class="fa fa-tag mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.expense_type")
                    </a>
                </li>
                @endcan
                <hr>
                @can('view_room_plan')
                <li class="app-sidebar__heading">@lang("message.header.report")</li>
                @endcan
        
                @can('view_item_sub_category')
                <li>
                    <a href="{{route('admin.daily_sales.index')}}"  class="menu-list-member  @yield('daily-sale-active')">
                        <i class="fa fa-list mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.daily_sales")
                    </a>
                </li>
                @endcan
                <hr>
                @can('view_item')
                <li>
                    <a href="{{route('admin.remain_items.index')}}"  class="menu-list-member  @yield('remaining-item-active')">
                        <i class="fa fa-list mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.remaining_item_list")
                    </a>
                </li>
                @endcan
                <hr>
                @can('view_item')
                <li>
                    <a href="{{route('admin.order_lists.index')}}"  class="menu-list-member  @yield('order-list-active')">
                        <i class="fa fa-list mr-3" style="font-size: 20px" ></i>
                        @if(count($neworder))
                        <span class="badge badge-pill badge-success">{{count($neworder)}}</span>
                        @endif
                        @lang("message.header.order_list")
                       
                    </a>
                   
                </li>
                @endcan
                <hr>
                <li>
                    <a href="{{route('admin.credit_reports.index')}}"  class="menu-list-member  @yield('credit-active')">
                        <i class="fa fa-list mr-3" style="font-size: 20px" ></i>
                        @lang('message.header.credit_report')
                    </a>
                </li>
                <hr>
              
                 @can('view_message')
                <li class="app-sidebar__heading">@lang("message.header.activity_log")</li>
                <li>
                    <a href="{{route('admin.activity_log.index')}}"  class="menu-list-member  @yield('activity-log-active')">
                        <i class="fa fa-eye mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.activity_log")                  
                    </a>
                </li>
                @endcan
                <hr>
                @can('view_discount')
                <li class="app-sidebar__heading">@lang("message.header.bussiness_info")</li>
                <li>
                    <a href="{{route('admin.bussiness_infos.index')}}"  class="menu-list-member  @yield('bussiness-info-active')">
                        <i class="fa fa-briefcase mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.bussiness_info")
                    </a>
                </li>
                @endcan
                <hr>
                @can('view_discount')
                <li class="app-sidebar__heading">@lang("message.price_management")</li>
                <li>
                    <a href="{{route('admin.discounts.index')}}"  class="menu-list-member  @yield('discount-active')">
                        <i class="fa fa-tags mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.discount")
                    </a>
                </li>
                @endcan
                <hr>

                   {{-- @can('view_earlylatecheck')
                <li>
                    <a href="{{route('admin.earlylatechecks.index')}}"  class="menu-list-member  @yield('shop-storage-active')">
                        <i class="metismenu-icon pe-7s-clock"></i>
                        Early / Late-Check Prices
                    </a>
                </li>
                @endcan --}}

                @can('view_user')
                 <li class="app-sidebar__heading">@lang("message.header.management")   </li>
                <li>
                    <a href="{{route('admin.client-users.index')}}"  class="menu-list-member  @yield('customer-active')">
                        <i class="fa fa-users mr-3" style="font-size: 20px" ></i>
                         @lang("message.header.customer")   
                    </a>
                </li>
                @endcan
                <hr>
                @can('view_user')
               <li>
                   <a href="{{route('admin.suppliers.index')}}"  class="menu-list-member  @yield('supplier-active')">
                    <i class="fa fa-users mr-3" style="font-size: 20px" ></i>
                    @lang("message.header.supplier")   
                   </a>
               </li>
               @endcan
               <hr>
                {{-- @can('view_user_nrc_image')
               <li>
                   <a href="{{route('admin.usernrcimages.index')}}"  class="menu-list-member  @yield('user-nrc-active')">
                       <i class="metismenu-icon pe-7s-photo"></i>
                       User Nrc Image
                   </a>
               </li>
               @endcan  --}}

                @can('view_account_type')
                <li>
                    <a href="{{route('admin.accounttypes.index')}}"  class="menu-list-member  @yield('account-type-active')">
                        <i class="fa fa-user-circle mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.customer_type")   
                    </a>
                </li>
                @endcan
                <hr>
                @can('view_tax')
                <li class="app-sidebar__heading">@lang("message.header.setting")   </li>
                <li>
                    <a href="{{route('admin.taxes.index')}}"  class="menu-list-member  @yield('tax-active')">
                        <i class="fa fa-filter mr-3" style="font-size: 20px" ></i>
                        @lang("message.header.tax")   
                    </a>
                </li>
                @endcan

                @can('view_admin')
                <li class="app-sidebar__heading">Admin User Management</li>
                <li>
                    <a href="{{route('admin.admin-users.index')}}"  class="menu-list-member  @yield('admin-user-active')">
                        <i class="fa fa-user mr-3" style="font-size: 20px" ></i>
                        Admin Users
                    </a>
                </li>
                @endcan
                @can('view_admin_user_roles')
                <li>
                    <a href="{{route('admin.roles.index')}}?guard={{config('custom_guards.default.admin')}}"
                         class="menu-list-member  @yield('admin-role-active')">
                         <i class="fa fa-user mr-3" style="font-size: 20px" ></i>
                         Admin Users Roles
                    </a>
                </li>
                @endcan
                {{-- @can('view_permission')
                <li class="app-sidebar__heading">Permission Management</li>
                <li>
                    <a href="{{route('admin.permission-group.index')}}?guard=admin"  class="menu-list-member  @yield('permission-active')">
                        <i class="fa fa-check-circle mr-3" style="font-size: 20px" ></i>
                        Permissions
                    </a>
                </li>
                @endcan --}}
            </ul>
        </div>
    </div>
</div>
