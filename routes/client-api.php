<?php

use App\Http\Controllers\LoginClientController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth/client'
], function () {
    Route::post('login', [LoginClientController::class, 'Login']);
    Route::group([
        'middleware' => ['auth:user-api', 'scopes:user']
    ], function () {
        Route::delete('logout', [LoginClientController::class, 'logout']);
        Route::get('me', [LoginClientController::class, 'user']);
    });
});
