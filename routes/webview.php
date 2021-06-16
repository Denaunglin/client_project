<?php

Route::namespace ('Webview')
    ->group(function () {
        Route::get('/webview/hotel-policies', 'PageController@hotelPolicies');
        Route::get('/webview/about-us', 'PageController@aboutUs');
        Route::get('/webview/terms-conditions', 'PageController@termsAndConditions');
    });

//Language Change
Route::get('locale/{locale}', function ($locale) {
    Session::put('locale', $locale);
    return redirect()->back();
});
