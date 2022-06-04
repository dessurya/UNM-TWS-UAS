<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// use App\Http\Controllers\InboxController;

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

Route::get('/inbox', 'InboxController@list');
Route::post('/inbox/store', 'InboxController@create');
Route::put('/inbox/store/{id}', 'InboxController@update');
Route::get('/inbox/{id}', 'InboxController@find');
Route::delete('/inbox/{id}', 'InboxController@delete');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
