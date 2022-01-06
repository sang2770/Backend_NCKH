<?php

use App\Http\Controllers\LoginController;
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

Route::group([
    'prefix' => 'auth/admin'
], function () {
    Route::post('login', [LoginController::class, 'Login']);
    // Route::post('signup', [LoginController::class, 'signup']);
    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::delete('logout', [LoginController::class, 'logout']);
        Route::get('me', [LoginController::class, 'user']);
    });
});
