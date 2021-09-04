<?php

use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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

Route::group(['middleware' => ['auth:api']], function () {
    // Resources
    Route::apiResource('role', RoleController::class);
    Route::apiResource('user', UserController::class);
    Route::apiResource('menu', MenuController::class);

    // Authenticated
    Route::post('logout', [UserController::class, 'logout']);
    Route::get('me', [UserController::class, 'getProfile']);
    Route::post('me', [UserController::class, 'postProfile']);

    // Others
    Route::post('menu/move', [MenuController::class, 'move']);
    Route::get('permission', [RoleController::class, 'getPermissions']);
});

Route::group(['middleware' => ['guest:api']], function () {
    // Guest
    Route::post('login', [UserController::class, 'login'])->name('login');
    Route::post('refresh', [UserController::class, 'refresh']);
    Route::get('redirect-uri', [UserController::class, 'getRedirectURI']);
    Route::post('handle-callback', [UserController::class, 'handleCallback']);
});
