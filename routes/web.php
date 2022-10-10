<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'App\Http\Controllers\IndexController');
Route::get('/edit/{id}', 'App\Http\Controllers\EditController')->name('edit');
