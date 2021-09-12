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

Route::get('/', function () {
    return view('welcome');
});

Route::get('push', 'MQController@push');
Route::get('pushSleep', 'MQController@pushSleep');

Route::get('demo', 'DemoController@product');

Route::get('job', 'JobController@index');

