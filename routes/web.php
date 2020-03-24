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
Route::get('/create_listing', 'HomeController@listing_page');
Route::post('/create_property', 'HomeController@create_property');
Route::get('/view_properties', 'GeneralController@view_property');
Route::get('/view_property/{id?}', 'GeneralController@view_one_property')->where('id', '[0-9]+');

//booking functions
Route::get('/book', 'HomeController@book');
Route::post('/create_booking', 'HomeController@create_booking');

//user profile functions
Route::get('/user_profile/{id?}', 'GeneralController@view_user');

//review functions
Route::get('/property_reviews', 'HomeController@property_reviews');
Route::get('/tennant_reviews', 'HomeController@tennant_reviews');
