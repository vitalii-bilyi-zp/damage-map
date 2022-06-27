<?php

use App\Http\Controllers\Api\CommunitiesController;
use App\Http\Controllers\Api\ObjectTypesController;

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

Route::get('/communities', [CommunitiesController::class, 'index']);
Route::get('/object-types', [ObjectTypesController::class, 'index']);
