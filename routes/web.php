<?php

// No Auth Need
Route::get('/', 'Backend\Admin\Auth\LoginController@showLoginForm')->name('/');
Route::post('/login', 'Backend\Admin\Auth\LoginController@login')->name('login');

//Language Change
Route::get('locale/{locale}', function ($locale) {
    Session::put('locale', $locale);
    return redirect()->back();
});
