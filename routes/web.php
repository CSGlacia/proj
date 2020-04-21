<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'GeneralController@view_property');

Auth::routes();

Route::get('/get_user_id', 'GeneralController@get_user_id');

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/home', 'GeneralController@home');

//property functions
Route::get('/create_property_page', 'HomeController@listing_page');
Route::post('/create_property', 'HomeController@create_property');
Route::get('/view_properties', 'GeneralController@view_property');
//TODO: log view data
Route::get('/view_property/{id?}', 'GeneralController@view_one_property')->where('id', '[0-9]+');
Route::get('/create_property_listing','HomeController@create_property_listing');
Route::post('/create_property_listing','HomeController@create_property_listing');
Route::post('/upload_property_images/{property_id?}', 'HomeController@upload_property_images')->where('id', '[0-9]+');
Route::get('/edit_property/{property_id?}', 'HomeController@edit_property')->where('id', '[0-9]+');
Route::post('/update_property', 'HomeController@update_property');
Route::post('/update_property_listing', 'HomeController@update_property_listing');
Route::post('/remove_property_images/{property_id?}', 'HomeController@remove_property_images')->where('id', '[0-9]+');

//property search
//TODO log search data
Route::post('/property_search', 'GeneralController@property_search');
//map search
//TODO log search data
Route::post('/map_search', 'GeneralController@map_search');

//booking functions
Route::post('/create_booking', 'HomeController@create_booking');
Route::get('/view_booking/{booking_id?}', 'HomeController@view_booking')->where('booking_id', '[0-9]+');
Route::get('/approve_booking/{booking_id?}', 'HomeController@approve_booking')->where('booking_id', '[0-9]+');
Route::get('/deny_booking/{booking_id?}', 'HomeController@deny_booking')->where('booking_id', '[0-9]+');

//user profile functions
Route::get('/user_profile/{id?}', 'GeneralController@view_user')->where('id', '[0-9]+');
Route::post('/cancel_booking', 'HomeController@cancel_booking');


//review functions
Route::get('/property_reviews', 'HomeController@property_reviews');
Route::get('/tennant_reviews', 'HomeController@tennant_reviews');
Route::get('/review_tennant', 'HomeController@review_tennant');
Route::get('/review_property', 'HomeController@review_property');
Route::post('/create_property_review', 'HomeController@create_property_review');
Route::post('/create_tennant_review', 'HomeController@create_tennant_review');
Route::get('/edit_tennant_review/{review_id?}', 'HomeController@edit_tennant_review')->where('review_id', '[0-9]+');
Route::post('/update_tennant_review', 'HomeController@update_tennant_review');
Route::get('/edit_property_review/{review_id?}', 'HomeController@edit_property_review')->where('review_id', '[0-9]+');
Route::post('/update_property_review', 'HomeController@update_property_review');
// Wishlist handlers
Route::get('/view_wishlist', 'HomeController@view_wishlist');
Route::post('/add_to_wishlist', 'HomeController@add_to_wishlist');
Route::post('/delete_wishlist', 'HomeController@delete_wishlist');


//tag functions
Route::post('/get_property_tags', 'GeneralController@get_property_tags');


// Deletion
Route::post('delete_property', 'HomeController@delete_property');
//  Where should this go?
// Route::post('delete_listing', 'HomeContoller@delte_listing');

//Admin functionality 
Route::get('/list_bookings','AdminController@all_bookings');
Route::get('/list_reviews','AdminController@all_reviews');
Route::post('/admin_delete_bookings','AdminController@admin_delete_bookings');
Route::post('/admin_delete_tennant_review','AdminController@admin_delete_tennant_review');
Route::post('/admin_delete_property_review','AdminController@admin_delete_property_review');

Route::get('/admin_advertiser','AdminController@create_advertiser');
Route::post('/admin_advertiser','AdminController@create_advertiser');
Route::get('/hello','AdminController@creater');
Route::get('/become_admin','AdminController@become_admin');

// email
Route::post('apply-two', ['uses'=>'NewsLetterController@autoMail', 'as'=>'apply-two']);


//exports
Route::get('/exports', 'AdvertiserController@exports');
Route::get('/download_search_data', 'AdvertiserController@download_search_data');
Route::get('/download_georgraphical_search_data', 'AdvertiserController@download_georgraphical_search_data');
Route::get('/download_booking_data', 'AdvertiserController@download_booking_data');
Route::get('/download_personal_data', 'AdvertiserController@download_personal_data');
Route::get('/download_viewing_data', 'AdvertiserController@download_viewing_data');

