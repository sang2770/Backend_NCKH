<?php

use App\Http\Controllers\FileManagementController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RequestManagementController;
use App\Http\Controllers\StudentManagementController;
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


// Login
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
/*
-----------------
Quản lý sinh viên
-----------------
*/
Route::prefix('student-management')->middleware('auth:api')->group(
    function () {
        Route::post('user', [StudentManagementController::class, 'store'])->name('Add');
        Route::post('users', [StudentManagementController::class, 'storeImport'])->name('AddList');
        Route::put('user', [StudentManagementController::class, 'update'])->name('UpdateOne');
        Route::put('users', [StudentManagementController::class, 'updateImport'])->name('UpdateList');
        Route::get('users', [StudentManagementController::class, 'index'])->name('GetList');
        Route::get('user', [StudentManagementController::class, 'show'])->name('GetOne');
        Route::post('majors', [StudentManagementController::class, 'indexMajors-'])->name('GetMajors');
        Route::get('majors-key', [StudentManagementController::class, 'indexMajorsKey'])->name('GetMajorsKey');
    }
);
/*
-----------------
Quản lý yêu cầu sinh viên
-----------------
*/
Route::prefix('request-management')->middleware('auth:api')->group(
    function () {
        Route::get('confirmed', [RequestManagementController::class, 'index'])->name('GetListconfirmed');
        Route::get('confirm', [RequestManagementController::class, 'indexConfirm'])->name('GetListConfirm');
        Route::post('confirm', [RequestManagementController::class, 'confirm'])->name('Confirm');
    }
);
/*
-----------------
Quản lý danh sách file mẫu
-----------------
*/
Route::prefix('file-management')->middleware('auth:api')->group(
    function () {
        Route::get('files', [FileManagementController::class, 'index'])->name('GetAllFile');
        Route::post('file', [FileManagementController::class, 'store'])->name('CreateFile');
    }
);
