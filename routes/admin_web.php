<?php

Route::get('/get_item','Backend\Admin\BuyingItemController@getItem');

Route::name('admin.')   
    ->prefix(config('app.prefix_admin_url') . '/admin')
    ->namespace('Backend\Admin')
    ->group(function () {
        
        // Auth
        Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
        Route::post('/login', 'Auth\LoginController@login')->name('login');

        Route::middleware(['auth:admin'])->group(function () {
            Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

            /*-- Permission Group --*/
            Route::resource('permission-group', 'PermissionGroupController');
            Route::get('/permission-group/datatable/ssd', 'PermissionGroupController@ssd');

            /*-- Permission --*/
            Route::resource('permission', 'PermissionController');

            //Cart 
            Route::post('/transcation/addproduct/{id}', 'TransactionController@addProductCart');
            Route::post('/transcation/removeproduct/{id}', 'TransactionController@removeProductCart');
            Route::post('/transcation/clear', 'TransactionController@clear');
            Route::post('/transcation/increasecart/{id}', 'TransactionController@increasecart');
            Route::post('/transcation/decreasecart/{id}', 'TransactionController@decreasecart');
            Route::post('/transcation/bayar','TransactionController@bayar');
            Route::get('/transcation/history','TransactionController@history');
            Route::get('/transcation/laporan/{id}','TransactionController@laporan');
            Route::get('/transcation','IndexController@index');
            //-- Role --
            Route::resource('roles', 'RolesController');

            Route::resource('item_categories', 'ItemCategoryController');
            Route::get('/item_categories/{item_category}/trash', 'ItemCategoryController@trash')->name('item_categories.trash');
            Route::get('/item_categories/{item_category}/restore', 'ItemCategoryController@restore')->name('item_categories.restore');
            Route::get('/item_categories/{item_category}/', 'ItemCategoryController@destroy')->name('item_categories.destroy');

            Route::resource('remain_items', 'RemainItemController');
            Route::get('/remain_items/{remain_item}/trash', 'RemainItemController@trash')->name('remain_items.trash');
            Route::get('/remain_items/{remain_item}/restore', 'RemainItemController@restore')->name('remain_items.restore');
            Route::get('/remain_items/{remain_item}/', 'RemainItemController@destroy')->name('remain_items.destroy');

            Route::get('/reorder_list','ItemController@reorderList')->name('reorder_list');
          
            Route::resource('/daily_sales','DailySellReportController');

            Route::resource('accounttypes', 'AccountTypeController');
            Route::get('/accounttypes/{accounttype}/trash', 'AccountTypeController@trash')->name('accounttypes.trash');
            Route::get('/accounttypes/{accounttype}/restore', 'AccountTypeController@restore')->name('accounttypes.restore');
            Route::get('/accounttypes/{accounttype}/', 'AccountTypeController@destroy')->name('accounttypes.destroy');

            Route::resource('expense_categories', 'ExpenseCategoryController');
            Route::get('/expense_categories/{expense_category}/trash', 'ExpenseCategoryController@trash')->name('expense_categories.trash');
            Route::get('/expense_categories/{expense_category}/restore', 'ExpenseCategoryController@restore')->name('expense_categories.restore');
            Route::get('/expense_categories/{expense_category}/', 'ExpenseCategoryController@destroy')->name('expense_categories.destroy');

            Route::resource('expense_types', 'ExpenseTypeController');
            Route::get('/expense_types/{expense_type}/trash', 'ExpenseTypeController@trash')->name('expense_types.trash');
            Route::get('/expense_types/{expense_type}/restore', 'ExpenseTypeController@restore')->name('expense_types.restore');
            Route::get('/expense_types/{expense_type}/', 'ExpenseTypeController@destroy')->name('expense_types.destroy');

            Route::resource('expenses', 'ExpenseController');
            Route::put('/expenses/{expense}/trash', 'ExpenseController@trash')->name('expenses.trash');
            Route::put('/expenses/{expense}/restore', 'ExpenseController@restore')->name('expenses.restore');
            Route::get('/expenses/{expense}/', 'ExpenseController@destroy')->name('expenses.destroy');

            Route::resource('bussiness_infos', 'BussinessInfoController');
            Route::get('/bussiness_infos/{bussiness_info}/trash', 'BussinessInfoController@trash')->name('bussiness_infos.trash');
            Route::get('/bussiness_infos/{bussiness_info}/restore', 'BussinessInfoController@restore')->name('bussiness_infos.restore');
            Route::get('/bussiness_infos/{bussiness_info}/', 'BussinessInfoController@destroy')->name('bussiness_infos.destroy');

            Route::resource('item_sub_categories', 'ItemSubCategoryController');
            Route::get('/item_sub_categories/{item_sub_category}/trash', 'ItemSubCategoryController@trash')->name('item_sub_categories.trash');
            Route::get('/item_sub_categories/{item_sub_category}/restore', 'ItemSubCategoryController@restore')->name('item_sub_categories.restore');
            Route::get('/item_sub_categories/{item_sub_category}/', 'ItemSubCategoryController@destroy')->name('item_sub_categories.destroy');

            Route::resource('credit_reports', 'CreditController');
            Route::get('/credit_reports/{credit_report}/trash', 'CreditController@trash')->name('credit_reports.trash');
            Route::get('/credit_reports/{credit_report}/restore', 'CreditController@restore')->name('credit_reports.restore');
            Route::get('/credit_reports/{credit_report}/delete', 'CreditController@destroy')->name('credit_reports.destroy');
            Route::get('/credit_reports/{credit_report}/detail', 'CreditController@detail')->name('credit_reports.detail');

            Route::resource('order_lists', 'OrderListController');
            Route::get('/order_lists/{order_list}/trash', 'OrderListController@trash')->name('order_lists.trash');
            Route::get('/order_lists/{order_list}/restore', 'OrderListController@restore')->name('order_lists.restore');
            Route::get('/order_lists/{order_list}/', 'OrderListController@destroy')->name('order_lists.destroy');

            Route::resource('item_ledgers', 'ItemLedgerController');
            Route::put('/item_ledgers/{item_ledger}/trash', 'ItemLedgerController@trash')->name('item_ledgers.trash');
            Route::put('/item_ledgers/{item_ledger}/restore', 'ItemLedgerController@restore')->name('item_ledgers.restore');
            Route::get('/item_ledgers/{item_ledger}/', 'ItemLedgerController@destroy')->name('item_ledgers.destroy');

            Route::resource('buying_items', 'BuyingItemController');
            Route::put('/buying_items/{buying_item}/trash', 'BuyingItemController@trash')->name('buying_items.trash');
            Route::put('/buying_items/{buying_item}/', 'BuyingItemController@destroy')->name('buying_items.destroy');
            Route::get('/buying_items/{buying_item}/restore', 'BuyingItemController@restore')->name('buying_items.restore');
            Route::post('buying_items/{buying_item}/update', 'BuyingItemController@update')->name('buying_items.update');

            Route::resource('opening_items', 'OpeningItemController');
            Route::get('/opening_items/{opening_item}/trash', 'OpeningItemController@trash')->name('opening_items.trash');
            Route::get('/opening_items/{opening_item}/', 'OpeningItemController@destroy')->name('opening_items.destroy');
            Route::get('/opening_items/{opening_item}/restore', 'OpeningItemController@restore')->name('opening_items.restore');
            Route::post('opening_items/{opening_item}/update', 'OpeningItemController@update')->name('opening_items.update');

            Route::resource('return_items', 'ReturnItemController');
            Route::get('/return_items/{return_item}/trash', 'ReturnItemController@trash')->name('return_items.trash');
            Route::get('/return_items/{return_item}/', 'ReturnItemController@destroy')->name('return_items.destroy');
            Route::get('/return_items/{return_item}/restore', 'ReturnItemController@restore')->name('return_items.restore');
            Route::post('return_items/{return_item}/update', 'ReturnItemController@update')->name('return_items.update');

            Route::resource('sell_items', 'sellItemController');
            Route::post('/sell_items/add_credit','sellItemController@addCredit')->name('sell_items.add_credit');
            Route::put('/sell_items/{sell_item}/trash', 'sellItemController@trash')->name('sell_items.trash');
            Route::put('/sell_items/{sell_item}/restore', 'sellItemController@restore')->name('sell_items.restore');
            Route::get('/sell_items/{sell_item}/', 'sellItemController@destroy')->name('sell_items.destroy');
            Route::get('index/sell_items/','sellItemController@indexSell');
            Route::get('credit/sell_items/','sellItemController@creditSellView');
            Route::get('sell_items/create/retail','sellItemController@createRetail')->name('sell_items.create_retail');
            Route::get('sell_items/create/wholesale','sellItemController@createWholesale')->name('sell_items.create_wholesale');

            Route::resource('services', 'ServiceController');
            Route::put('/services/{service}/trash', 'ServiceController@trash')->name('services.trash');
            Route::put('/services/{service}/restore', 'ServiceController@restore')->name('services.restore');
            Route::get('/services/{service}/', 'ServiceController@destroy')->name('services.destroy');

        
            Route::resource('payslips', 'payslipController');
            Route::get('/payslips/{payslip}/trash', 'payslipController@trash')->name('payslips.trash');
            Route::get('/payslips/{payslip}/restore', 'payslipController@restore')->name('payslips.restore');
            Route::get('/payslips/{payslip}/', 'payslipController@destroy')->name('payslips.destroy');
            Route::get('/payslips/{payslip}/mark-as-read', 'payslipController@markasread')->name('payslips.mark-as-read');
            Route::get('/payslips/{payslip}/detail', 'payslipController@show')->name('payslips.detail');
            Route::get('/payslips/{booking}/status_detail', 'BookingController@status');
            Route::get('/payslips/{payslip}/change_status', 'payslipController@change_status')->name('payslips.change_status');

            Route::get('/item/list/datatable/ssd', 'IndexController@itemSsd');
       
            Route::resource('invoices', 'InvoiceController');
            Route::get('/invoices/{invoice}/trash', 'InvoiceController@trash')->name('invoices.trash');
            Route::get('/invoices/{invoice}/restore', 'InvoiceController@restore')->name('invoices.restore');
            Route::get('/invoices/{invoice}/', 'InvoiceController@destroy')->name('invoices.destroy');
            Route::get('/invoices/{invoice}/detail', 'InvoiceController@detail')->name('invoices.detail');

            // Route::resource('/final_pays','IndexController');   
            Route::get('/final_pays','IndexController@show');
            Route::get('/final_pays/ssd','IndexController@finalPay');

            Route::resource('sendnotifications', 'sendNotificationController');
            Route::get('/sendnotifications/{sendnotification}/trash', 'sendNotificationController@trash')->name('sendnotifications.trash');
            Route::get('/sendnotifications/{sendnotification}/restore', 'sendNotificationController@restore')->name('sendnotifications.restore');
            Route::get('/sendnotifications/{sendnotification}/', 'sendNotificationController@destroy')->name('sendnotifications.destroy');
            Route::get('/sendnotifications/{sendnotification}/detail', 'sendNotificationController@detail')->name('sendnotifications.detail');

            Route::resource('usernrcimages', 'UserNrcPictureController');
            Route::get('/usernrcimages/{usernrcimage}/trash', 'UserNrcPictureController@trash')->name('usernrcimages.trash');
            Route::get('/usernrcimages/{usernrcimage}/restore', 'UserNrcPictureController@restore')->name('usernrcimages.restore');

            Route::get('/service/invoices/{id}','InvoiceController@printServiceInvoice')->name('service_invoices');
            Route::get('/sells/invoices/{id}','InvoiceController@printSalesInvoice')->name('sell_invoice');

            Route::resource('taxes', 'TaxController');
            Route::get('/taxes/{tax}/trash', 'TaxController@trash')->name('taxes.trash');
            Route::get('/taxes/{tax}/restore', 'TaxController@restore')->name('taxes.restore');
            Route::get('/taxes/{tax}/', 'TaxController@destroy')->name('taxes.destroy');

            Route::resource('deposits', 'DepositController');
            Route::get('/deposits/{deposit}/trash', 'DepositController@trash')->name('deposits.trash');
            Route::get('/deposits/{deposit}/restore', 'DepositController@restore')->name('deposits.restore');
            Route::get('/deposits/{deposit}/', 'DepositController@destroy')->name('deposits.destroy');

            Route::resource('discounts', 'DiscountController');
            Route::get('/discounts/{discount}/trash', 'DiscountController@trash')->name('discounts.trash');
            Route::get('/discounts/{discount}/restore', 'DiscountController@restore')->name('discounts.restore');
            Route::get('/discounts/{discount}/', 'DiscountController@destroy')->name('discounts.destroy');

            Route::post('projects/media', 'ProjectsController@storeMedia')->name('projects.storeMedia');

            Route::middleware('optimizeImages')->group(function () {
                Route::resource('items', 'ItemController');
            });
            Route::put('/items/{item}/trash', 'ItemController@trash')->name('items.trash');
            Route::put('/items/{item}/restore', 'ItemController@restore')->name('items.restore');
            Route::get('/items/{item}/detail', 'ItemController@detail')->name('items.detail');

            Route::resource('admin-users', 'AdminUsersController');
            Route::put('/admin-users/{admin_user}/trash', 'AdminUsersController@trash')->name('admin-users.trash');
            Route::put('/admin-users/{admin_user}/restore', 'AdminUsersController@restore')->name('admin-users.restore');

            Route::resource('client-users', 'ClientUsersController');
            Route::put('/client-users/{client_user}/trash', 'ClientUsersController@trash')->name('client-users.trash');
            Route::put('/client-users/{client_user}/restore', 'ClientUsersController@restore')->name('client-users.restore');
            Route::get('/clients-users/{client_user}/detail', 'ClientUsersController@detail')->name('client-users.detail');
            Route::post('clients-users/{client_user}/update', 'ClientUsersController@update')->name('client-users.update');


            Route::resource('suppliers', 'SupplierController');
            Route::put('/suppliers/{supplier}/trash', 'SupplierController@trash')->name('suppliers.trash');
            Route::put('/suppliers/{supplier}/restore', 'SupplierController@restore')->name('suppliers.restore');
            Route::get('/suppliers/{supplier}/detail', 'SupplierController@detail')->name('suppliers.detail');
            Route::post('suppliers/{supplier}/update', 'SupplierController@update')->name('suppliers.update');

            Route::resource('shop_storages', 'ShopStorageController');
            Route::put('/shop_storages/{shop_storage}/trash', 'ShopStorageController@trash')->name('shop_storages.trash');
            Route::put('/shop_storages/{shop_storage}/restore', 'ShopStorageController@restore')->name('shop_storages.restore');
            Route::get('/shop_storages/{shop_storage}/delete', 'ShopStorageController@destroy')->name('shop_storages.delete');
            Route::get('/shop_storages/{shop_storage}/detail', 'ShopStorageController@detail')->name('shop_storages.detail');
            Route::post('shop_storages/{shop_storage}/update', 'ShopStorageController@update')->name('shop_storages.update');

            Route::resource('cash_books', 'CashbookController');
            Route::get('/cash_books/{cash_book}/delete', 'CashbookController@destroy')->name('cash_books.delete');

            Route::resource('profit_lost', 'ProfitLostController');

            Route::get('/', 'IndexController@index')->name('index');

            Route::resource('activity_log', 'activityLogController');
            Route::get('/activity_log/{activity_log}/', 'activityLogController@destroy')->name('activity_log.trash');

        });

        Route::get('locale/{locale}', function ($locale) {
            Session::put('locale', $locale);
            return redirect()->back();
        });

    });
