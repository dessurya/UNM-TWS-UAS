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

Route::get('/inbox', 'InboxController@list')->name('inbox-list');
Route::post('/inbox/store', 'InboxController@create')->name('inbox-store');
Route::put('/inbox/store', 'InboxController@update')->name('inbox-update');
Route::get('/inbox/open', 'InboxController@find')->name('inbox-open');
Route::delete('/inbox', 'InboxController@delete')->name('inbox-delete');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
