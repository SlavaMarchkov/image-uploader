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

Route::namespace('App\Http\Controllers\Post')->prefix('posts')->group(function () {
    Route::namespace('Image')->prefix('images')->group(function () {
        Route::post('/', 'StoreController');
    });
    Route::get('/{id}', 'EditController');
    Route::post('/', 'StoreController');
    Route::patch('/{post}', 'UpdateController');
    Route::get('/', 'IndexController');
});
