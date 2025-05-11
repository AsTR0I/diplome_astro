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

// cdr
Route::get('calls', 'CdrController@index');
Route::get('calls/calls-count', 'CdrController@dashboardChart');
Route::get('calls/export-to-xlsx', 'CdrController@exportToXLSX');
Route::get('calls/recordings/{uniqueid}', 'CdrController@getRecording');

// dialplans
Route::get('dialplans', 'DialplanController@index');
Route::post('dialplans', 'DialplanController@store');
Route::get('dialplans/{id}', 'DialplanController@edit');
Route::post('dialplans/{id}', 'DialplanController@update');
Route::post('dialplans/{id}/delete', 'DialplanController@destroy');

// Extensions
Route::get('extensions', 'ExtensionController@index');
Route::post('extensions', 'ExtensionController@store');
Route::get('extensions/contexts', 'ExtensionController@getContexts');
Route::get('extensions/{id}', 'ExtensionController@edit');
Route::post('extensions/{id}', 'ExtensionController@update');
Route::post('extensions/{id}/delete', 'ExtensionController@destroy');

// sippeers
Route::get('sippeers', 'SippeerController@index');
Route::get('sippeers/export-to-xlsx', 'SippeerController@exportToXLSX');

Route::post('sippeers', 'SippeerController@store');
Route::get('sippeers/{id}', 'SippeerController@edit');
Route::post('sippeers/{id}', 'SippeerController@update');
Route::post('sippeers/{id}/delete', 'SippeerController@destroy');

// system
Route::get('system-info/resources','SystemInfoController@resources');
Route::get('system-info/cpu-chart','SystemInfoController@cpuChart');
Route::get('system-info/ram-chart','SystemInfoController@ramChart');
Route::get('system-info/os-resources','SystemInfoController@osResources');
Route::get('system-info/hard-drivers-resources','SystemInfoController@hardDriversResources');
Route::get('system-info/network-interfaces-resources','SystemInfoController@networkInterfacesResources');

Route::get('settings/users','UserController@index');
Route::post('settings/users/{id}/delete', 'UserController@destroy');

Route::get('settings/configs','ConfigController@index');
Route::get('settings/configs/files-content','ConfigController@getfileContent');
Route::post('settings/configs/files-content','ConfigController@updatefileContent');
Route::post('settings/configs/files/rename', 'ConfigController@rename');
Route::post('settings/configs/files/delete','ConfigController@delete');
Route::get('settings/configs/files/download','ConfigController@download');
Route::post('settings/configs/files/upload','ConfigController@upload');

Route::get('asterisk-commands','AsteriskCommandsController@index');
Route::post('asterisk-commands/{id}/execute', 'AsteriskCommandsController@execute');

Route::get('sniffer/sip-packets','SnifferController@index');
Route::get('sniffer/sip-packets/session','SnifferController@sipSession');