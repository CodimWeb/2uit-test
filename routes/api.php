<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// auth
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout',  [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
});

//public
Route::post('/registration', [UserController::class, 'create']);
Route::get('/persons/{storageType}', [PersonController::class, 'getAll']);

// auth-guard
Route::group(['middleware' => 'jwt.auth'], function () {
    Route::post('/person', [PersonController::class, 'create']);
});
