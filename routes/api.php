<?php

use App\Http\Controllers\Api\RegionsController;
use App\Http\Controllers\Api\CommunitiesController;
use App\Http\Controllers\Api\ObjectTypesController;
use App\Http\Controllers\Api\DamageNotesController;
use App\Http\Controllers\Api\StatisticsController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\RegulationDocumentsController;

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

Route::get('/regions', [RegionsController::class, 'index']);
Route::get('/object-types', [ObjectTypesController::class, 'index']);

Route::get('/damage-notes/regions', [DamageNotesController::class, 'showRegions']);
Route::get('/damage-notes/districts', [DamageNotesController::class, 'showDistricts']);
Route::get('/damage-notes/communities', [DamageNotesController::class, 'showCommunities']);

Route::get('/statistics/global', [StatisticsController::class, 'showGlobal']);
Route::get('/statistics/ratio', [StatisticsController::class, 'showRatio']);

Route::get('/regulation-documents', [RegulationDocumentsController::class, 'index']);

Route::post('/login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/users', [UsersController::class, 'index']);
    Route::post('/users', [UsersController::class, 'store']);
    Route::get('/users/{user}', [UsersController::class, 'show']);
    Route::put('/users/{user}', [UsersController::class, 'update']);
    Route::delete('/users/{user}', [UsersController::class, 'destroy']);

    Route::get('/roles', [RolesController::class, 'index']);

    Route::get('/communities', [CommunitiesController::class, 'index']);

    Route::get('/damage-notes/approved', [DamageNotesController::class, 'getApproved']);
    Route::get('/damage-notes/not-approved', [DamageNotesController::class, 'getNotApproved']);
    Route::post('/damage-notes', [DamageNotesController::class, 'store']);
    Route::post('/damage-notes/import-file', [DamageNotesController::class, 'storeFromFile']);
    Route::get('/damage-notes/export-csv', [DamageNotesController::class, 'exportCsv']);
    Route::get('/damage-notes/{damageNote}', [DamageNotesController::class, 'show']);
    Route::put('/damage-notes/{damageNote}', [DamageNotesController::class, 'update']);
    Route::delete('/damage-notes/{damageNote}', [DamageNotesController::class, 'destroy']);

    Route::post('/regulation-documents', [RegulationDocumentsController::class, 'store']);
    Route::delete('/regulation-documents/{regulationDocument}', [RegulationDocumentsController::class, 'destroy']);
});
