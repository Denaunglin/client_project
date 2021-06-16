<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group([
    'namespace' => 'Api\V1',
    'prefix' => 'v1',
], function () {

    Route::get('check-for-update', 'GeneralController@checkForUpdate');
    // Route::group([
    //     'middleware' => ['version_check'],
    // ], function () {
    // });

    Route::post('/register', 'AuthController@Register');
    Route::post('/login', 'AuthController@Login');
    Route::get('/user', 'AuthController@user')->middleware('auth:api');
    Route::post('forgot-password', 'AuthController@forgotPassword');
    Route::post('reset-password', 'AuthController@resetPassword');
    Route::get('/otp-resend', 'AuthController@resendOTP');

    Route::get('get_slider', 'PageController@getSlider');
    Route::get('explore-room', 'PageController@exploreRoom');
    Route::get('room-list', 'PageController@roomList');
    Route::get('get_room', 'PageController@getRoom');
    // Route::post('check-avalability', 'PageController@checkAvalability');
    Route::get('room-detail', 'PageController@roomDetail');
    Route::get('room-type', 'PageController@roomType');
    Route::get('booking-checkto', 'PageController@bookingCheckto');
    Route::post('booking-store', 'PageController@bookingStore');
    // Route::get('booking-store-detail', 'PageController@bookingStoreDetail');
    Route::post('payslip-store', 'PageController@payslipStore');
    Route::post('booking-retrieve', 'PageController@bookingRetrieve');
    Route::post('booking-cancel', 'ClientController@Cancellation');
    Route::post('contact-us', 'PageController@contactUs');

    Route::get('subscribe_noti', 'PageController@saveNoti');
    Route::get('unsubscribe_noti', 'PageController@unsubscribeNoti');

    // Route::get('hotel-policies', 'PageController@termsConditions');
    // Route::get('about-us', 'PageController@aboutUs');

    Route::group([
        'middleware' => ['auth:api'],
    ], function () {
        Route::post('logout', 'AuthController@logout');
        Route::get('profile', 'ClientController@profile');
        Route::get('profile-edit', 'ClientController@profileEdit');
        Route::post('profile-update', 'ClientController@profileUpdate');

        Route::get('booking', 'ClientController@booking');
        Route::get('booking-detail', 'ClientController@bookingDetail');

        Route::get('notification', 'ClientController@Notification');
        Route::get('notification/{notification_id}', 'ClientController@show');
        Route::get('notification/{notification_id}/markasread', 'ClientController@markAsRead');
        Route::get('notification/{notification_id}/delete', 'ClientController@notificationDelete');

        Route::post('profile/cardadd', 'ClientController@AddUserCard');
        Route::post('profile/cardupdate/{id}', 'ClientController@UserCardUpdate');
        Route::get('profile/carddelete/{id}', 'ClientController@UserCardDelete');

    });

});
