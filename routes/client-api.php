<?php

use App\Http\Controllers\LoginClientController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth/client'
], function () {
    Route::post('login', [LoginClientController::class, 'Login']);
    Route::post('forget-password', [LoginClientController::class, "Forgot"]);
    Route::post('reset-password', [LoginClientController::class, "Reset"]);
    Route::group([
        'middleware' => ['auth:user-api', 'scopes:user']
    ], function () {
        Route::delete('logout', [LoginClientController::class, 'logout']);
        Route::get('me', [LoginClientController::class, 'user']);
        Route::post('ResetPass', [LoginClientController::class, 'change']);
        Route::get('info-student/{id}', [StudentController::class, 'show']); //sinh vien xem thong tin ca nhan
        Route::post('request-student', [StudentController::class, 'store']); //sinh vien gui yeu cau xin giay xac nhan
        Route::get('register-student/{id}', [StudentController::class, 'register']); //sinh vien xem thong tin ve giay cn dky nvqs cua minh
        Route::get('notification-student', [StudentController::class, 'notification']); //danh sach thong bao cua ca nhan sinh vien
        Route::get('notificationID-student', [StudentController::class, 'notificationID']); //danh sach thong bao cua ca nhan sinh vien
        Route::get('list-request-student/{id}', [StudentController::class, 'showRequest']); //danh sach giay xac nhan tu truong
        Route::delete('delete-request-student/{id}/{msv}', [StudentController::class, 'DestroyRequest']); //danh sach giay xac nhan tu truong
    });
});
