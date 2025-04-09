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

Auth::routes();
Route::get('/', 'HomeController@index')->name('home');

Route::get('calls', 'CdrController@index');
Route::get('calls/calls-count', 'CdrController@dashboardChart');

Route::get('dialplans', 'DialplanController@index');
Route::post('dialplans', 'DialplanController@store');
Route::get('dialplans/{id}', 'DialplanController@edit');
Route::post('dialplans/{id}', 'DialplanController@update');
Route::post('dialplans/{id}/delete', 'DialplanController@destroy');

// Extensions
Route::get('extensions', 'ExtensionController@index');
Route::post('extensions', 'ExtensionController@store');
Route::get('extensions/{id}', 'ExtensionController@edit');
Route::post('extensions/{id}', 'ExtensionController@update');
Route::post('extensions/{id}/delete', 'ExtensionController@destroy');

Route::get('system-info/resources','SystemInfoController@resources');
Route::get('system-info/cpu-chart','SystemInfoController@cpuChart');
Route::get('system-info/ram-chart','SystemInfoController@ramChart');