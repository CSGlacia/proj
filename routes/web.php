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

//property functions
Route::get('/create_property_page', 'HomeController@listing_page');
Route::post('/create_property', 'HomeController@create_property');
Route::get('/view_properties', 'GeneralController@view_property');
Route::get('/view_property/{id?}', 'GeneralController@view_one_property')->where('id', '[0-9]+');
Route::get('/create_property_listing','HomeController@create_property_listing');
Route::post('/create_property_listing','HomeController@create_property_listing');

//booking functions
Route::post('/create_booking', 'HomeController@create_booking');

//user profile functions
Route::get('/user_profile/{id?}', 'GeneralController@view_user')->where('id', '[0-9]+');

//review functions
Route::get('/property_reviews', 'HomeController@property_reviews');
Route::get('/tennant_reviews', 'HomeController@tennant_reviews');
Route::get('/review_tennant', 'HomeController@review_tennant');
Route::get('/review_property', 'HomeController@review_property');
Route::post('/create_property_review', 'HomeController@create_property_review');
Route::post('/create_tennant_review', 'HomeController@create_tennant_review');

// Wishlist handlers

Route::get('/view_wishlist', 'HomeController@view_wishlist');
Route::post('/add_to_wishlist', 'HomeController@add_to_wishlist');
Route::post('/delete_wishlist', 'HomeController@delete_wishlist');


// Deletion
Route::post('delete_property', 'HomeController@delete_property');
//  Where should this go?
// Route::post('delete_listing', 'HomeContoller@delte_listing');
