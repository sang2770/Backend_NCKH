<?php

use App\Http\Controllers\FileManagementController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RequestManagementController;
use App\Http\Controllers\StudentManagementController;
use App\Http\Controllers\RegisterMilitaryController;
use App\Http\Controllers\ConfirmMilitaryController;
use App\Http\Controllers\LoginClientController;
use App\Http\Controllers\MoveMilitaryController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------

*/
// Login
Route::group([
    'prefix' => 'auth/admin'
], function () {
    Route::post('login', [LoginController::class, 'Login']);
    // Route::post('signup', [LoginController::class, 'signup']);
    Route::group([
        'middleware' => ['auth:admin-api', 'scopes:admin']
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
Route::prefix('student-management')->middleware(['auth:admin-api', 'scopes:admin'])->group(
    function () {
        Route::post('user', [StudentManagementController::class, 'store'])->name('Add');
        Route::post('users', [StudentManagementController::class, 'storeImport'])->name('AddList');
        Route::put('user/{id}', [StudentManagementController::class, 'update'])->name('UpdateOne');
        Route::put('users', [StudentManagementController::class, 'updateImport'])->name('UpdateList');
        Route::get('users', [StudentManagementController::class, 'index'])->name('GetList');
        Route::get('users-export', [StudentManagementController::class, 'exportIndex'])->name('Export');
        Route::get('user/{id}', [StudentManagementController::class, 'show'])->name('GetOne');
        Route::get('majors', [StudentManagementController::class, 'indexMajors'])->name('GetMajors');
        Route::get('majors-key', [StudentManagementController::class, 'indexMajorsKey'])->name('GetMajorsKey');
    }
);
/*
-----------------
Quản lý yêu cầu sinh viên
-----------------
*/
Route::prefix('request-management')->middleware(['auth:admin-api', 'scopes:admin'])->group(
    function () {
        Route::get('confirm', [RequestManagementController::class, 'index'])->name('GetListConfirm');
        Route::post('confirm', [RequestManagementController::class, 'confirm'])->name('Confirm');
    }
);
/*
-----------------
Quản lý danh sách file mẫu
-----------------
*/
Route::prefix('file-management')->middleware(['auth:admin-api', 'scopes:admin'])->group(
    function () {
        Route::get('files', [FileManagementController::class, 'index'])->name('GetAllFile');
        Route::get('file/{id}', [FileManagementController::class, 'DowloadFile'])->name('DowloadFile');
        Route::post('file', [FileManagementController::class, 'store'])->name('CreateFile');
    }
);

// nghia vu quan su
// 1. giay chung nhan dang ky nvqs
Route::group([
    'prefix' => 'register-military-management'
], function () {
    Route::post('store-register-military-file', [RegisterMilitaryController::class, 'StoreFile']); ///Import bang file
    Route::post('store-register-military', [RegisterMilitaryController::class, 'Store']);
});
// 2. giay xac nhan tu truong
Route::group([
    'prefix' => 'confirm-military-management'
], function () {
    Route::get('confirm-military', [ConfirmMilitaryController::class, 'Confirms']); //cap giay xac nhan cho nhieu sinh vien
    Route::get('confirm-military/{id}', [ConfirmMilitaryController::class, 'Confirm']); //cap giay xac nhan cho 1 sinh vien
});
// 3. giay di chuyen tu truong
Route::group([
    'prefix' => 'move-military-management'
], function () {
    Route::get('move-military', [MoveMilitaryController::class, 'Moves']); //cap giay di chuyen cho nhieu sinh vien
    Route::get('move-military/{id}', [MoveMilitaryController::class, 'Move']); //cap giay di chuyen cho 1 sinh vien
});

// thong bao
Route::group([
    'prefix' => 'notification-management'
], function () {
    Route::get('index-header-notification', [NotificationController::class, 'IndexHeader']); //lay ra danh sach tieu de thong bao
    Route::get('show-notification/{id}', [NotificationController::class, 'show']); //lay ra tieu de va noi dung thong bao voi $id
    Route::post('store-notification', [NotificationController::class, 'StoreNotification']);  //luu thong bao moi
    Route::put('update-notification/{id}', [NotificationController::class, 'UpdateNotification']); //update thong bao
    Route::delete('delete-notification/{id}', [NotificationController::class, 'DestroyNotification']); //xoa 1 thong bao
    Route::post('sent-notification-students', [NotificationController::class, 'SentNotificationStudent']); //gui thong bao den sinh vien
});


/*
|--------------------------------------------------------------------------
| Client
|--------------------------------------------------------------------------

*/
