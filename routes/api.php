<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//languages
Route::get('getall/languages', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'get_languages']);
//countries list
Route::get('getall/countries', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'get_countries']);
Route::post('user/request_otp', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'request_otp']);
Route::post('user/otp_verification', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'otp_verification']);
Route::get('getall/admins', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'getAdminusers']);
Route::post('update/profileimage', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'profileimage_update']);
Route::post('update/userprofile', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'userprofile_update']);
Route::get('view/userprofile', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'userprofile']);
Route::post('home', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'homeServices']);
Route::post('user/logout', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'logout']);
Route::post('parentservices', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'parentServices']);
Route::post('childservices', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'childServices']);
Route::get('couponlist', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'couponList']);
Route::post('subservices', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'subServices']);
Route::post('servicemanlist', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'ServicemanList']);
Route::post('place-order', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'placeOrder']);
Route::post('update/payment-status', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'paymentstatusUpdate']);
Route::post('coupon-validity', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'checkvalidCoupon']);
Route::post('address/create', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'create_useraddress']);
Route::post('address/update', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'update_useraddress']);
Route::post('address/show', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'show_useraddress']);
Route::post('address/delete', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'delete_useraddress']);

Route::get('address/list', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'get_Alluseraddress']);
Route::post('update/coverimage', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'coverimage_update']);
Route::post('update/location', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'update_location']);
Route::post('update/serviceman-profile', [App\Http\Controllers\FrontApp\ServiceApiController::class,'serviceman_profile_update']);
Route::post('serviceman-profile', [App\Http\Controllers\FrontApp\ServiceApiController::class,'serviceman_profile']);

Route::post('chat-list', [App\Http\Controllers\FrontApp\ServiceApiController::class,'chat_list']);

Route::post('favorite/add', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'Addfavorite']);
Route::post('favorite/remove', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'Removefavorite']);
Route::post('favorite/list', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'favoriteList']);
Route::post('chat-store', [App\Http\Controllers\FrontApp\ServiceApiController::class,'storeMessage']);
Route::post('chat-messages', [App\Http\Controllers\FrontApp\ServiceApiController::class,'chatMessages']);
Route::post('update/read-status', [App\Http\Controllers\FrontApp\ServiceApiController::class,'updateReadMessage']);

Route::post('view/other/userprofile', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'other_userprofile']);
Route::post('other/address/list', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'get_Otheruseraddress']);
Route::post('active-services', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'activeServices']);
Route::post('active-subscriptions', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'activeSubscriptions']);
Route::post('report-customer', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'report_customer']);
Route::post('remove/gallery-image', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'Remove_galleryimages']);
Route::post('payment-success', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'payment_success']);
Route::post('payment-failed', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'payment_failed']);
Route::post('payment-webhook', [App\Http\Controllers\FrontApp\ServiceApiController::class, 'payment_webhook']);





































