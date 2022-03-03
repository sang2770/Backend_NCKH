<?php

use App\Http\Controllers\FileManagementController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RequestManagementController;
use App\Http\Controllers\StudentManagementController;
use App\Http\Controllers\RegisterMilitaryController;
use App\Http\Controllers\ConfirmMilitaryController;
use App\Http\Controllers\ForgotPassWord;
use App\Http\Controllers\LoginClientController;
use App\Http\Controllers\MoveMilitaryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\MoveMilitaryLocalController;
use App\Http\Controllers\ReportController;
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
    Route::post('forget-password', [ForgotPassWord::class, "Forgot"]);
    Route::post('reset-password', [ForgotPassWord::class, "Reset"]);

    // Route::post('signup', [LoginController::class, 'signup']);
    Route::group([
        'middleware' => ['auth:admin-api', 'scopes:admin']
    ], function () {
        Route::delete('logout', [LoginController::class, 'logout']);
        Route::post('ChangePassword', [LoginController::class, 'change']);
        Route::get('me', [LoginController::class, 'user']);
        Route::post('signup', [LoginController::class, 'signup']);
    });
});
/*
|--------------------------------------------------------------------------
|Quản lý sinh viên
|--------------------------------------------------------------------------

*/
Route::prefix('student-management')->middleware(['auth:admin-api', 'scopes:admin'])->group(
    function () {
        Route::post('user', [StudentManagementController::class, 'store'])->name('Add');
        Route::post('users', [StudentManagementController::class, 'storeImport'])->name('AddList');
        Route::patch('user/{id}', [StudentManagementController::class, 'update'])->name('UpdateOne');
        Route::get('user-history/{id}', [StudentManagementController::class, 'userHistory'])->name('UpdateOne');
        Route::put('users', [StudentManagementController::class, 'updateImport'])->name('UpdateList');
        Route::get('users', [StudentManagementController::class, 'index'])->name('GetList');
        Route::get('users-export', [StudentManagementController::class, 'exportIndex'])->name('Export');
        Route::get('user/{id}', [StudentManagementController::class, 'show'])->name('GetOne');
        Route::get('majors', [StudentManagementController::class, 'indexMajors'])->name('GetMajors');
        Route::get('class', [StudentManagementController::class, 'indexClass'])->name('GetClass');
        Route::get('majors-key', [StudentManagementController::class, 'indexMajorsKey'])->name('GetMajorsKey');
        Route::post('majors', [StudentManagementController::class, 'importMajors']);
        Route::post('class', [StudentManagementController::class, 'importClass']);
    }
);
/*
|--------------------------------------------------------------------------
Quản lý yêu cầu sinh viên
|--------------------------------------------------------------------------

*/
Route::prefix('request-management')->middleware(['auth:admin-api', 'scopes:admin'])->group(
    function () {
        Route::get('confirm', [RequestManagementController::class, 'index'])->name('GetListConfirm');
        Route::post('confirm/{id}', [RequestManagementController::class, 'confirm'])->name('Confirm');
        Route::post('confirmIndex', [RequestManagementController::class, 'confirmIndex'])->name('ConfirmIndex');
    }
);
/*
|--------------------------------------------------------------------------
Quản lý danh sách file mẫu
|--------------------------------------------------------------------------

*/
Route::prefix('file-management')->middleware(['auth:admin-api', 'scopes:admin'])->group(
    function () {
        Route::get('files', [FileManagementController::class, 'index'])->name('GetAllFile');
        Route::get('file/{id}', [FileManagementController::class, 'DowloadFile'])->name('DowloadFile');
        Route::post('file', [FileManagementController::class, 'store'])->name('CreateFile');
    }
);
/*
|--------------------------------------------------------------------------
Xuất báo cáo
|--------------------------------------------------------------------------

*/
Route::prefix('export-report')->group(
    function () {
        Route::get('student-fluctuations', [ReportController::class, 'ExportFluctuations']);
        Route::get('student-update', [ReportController::class, 'ExportUpdate']);
        Route::get('student-import', [ReportController::class, 'ExportImport']);
        Route::get('export-student-fluctuations', [ReportController::class, 'ReportFluctuations']);
        Route::get('export-student-update', [ReportController::class, 'ReportUpdate']);
        Route::get('export-student-import', [ReportController::class, 'ReportImport']);
        Route::get('report-movemili', [ReportController::class, 'ReportMoveMilitary']);
        Route::get('report-confirm-mili', [ReportController::class, 'ReportConfirmMilitary']);
        Route::get('export-file-confirm-mili', [ReportController::class, 'ExportFileConfirm']);
        Route::get('export-file-move-mili', [ReportController::class, 'ExportFileMove']);

    }
);
// nghia vu quan su
/*
|--------------------------------------------------------------------------
giay chung nhan dang ky nvqs
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix' => 'register-military-management',
    'middleware' => ['auth:admin-api', 'scopes:admin']
], function () {
    Route::post('store-register-military-file', [RegisterMilitaryController::class, 'StoreFile']); ///Import bang file
    Route::post('store-register-military', [RegisterMilitaryController::class, 'Store']); //them moi
    Route::put('update-register-military/{id}', [RegisterMilitaryController::class, 'Update']);
    Route::get('filter-info-register', [RegisterMilitaryController::class, 'FilterRegister']);  //loc thong tin sinhvien cung thong tin giay cn dky
    Route::get('filter-info-confirm', [RegisterMilitaryController::class, 'FilterConfirm']); //loc thong tin giay xac nhan tu truong
    Route::get('filter-info-move', [RegisterMilitaryController::class, 'FilterMove']); //loc thong tin giay di chuyen nvqs tu truong
    Route::get('filter-info-move-local', [RegisterMilitaryController::class, 'FilterMoveLocal']);
});
/*
|--------------------------------------------------------------------------
 giay xac nhan tu truong
|--------------------------------------------------------------------------
*/
Route::group([
    'prefix' => 'confirm-military-management',
    'middleware' => ['auth:admin-api', 'scopes:admin']
], function () {
    Route::get('confirm-military', [ConfirmMilitaryController::class, 'Confirm']); //cap giay xac nhan cho 1 sinh vien
});
// 3. giay di chuyen tu truong
Route::group([
    'prefix' => 'move-military-management',
    'middleware' => ['auth:admin-api', 'scopes:admin'],
], function () {
    Route::get('move-military', [MoveMilitaryController::class, 'Move']); //cap giay di chuyen cho 1 sinh vien
});

// 4. giay di chuyen tu dia phuong
Route::group([
    'prefix' => 'move-military-local-management',
    'middleware' => ['auth:admin-api', 'scopes:admin']
], function () {
    Route::post('store-move-military-local-file', [MoveMilitaryLocalController::class, 'StoreFile']);
    Route::post('store-move-military-local', [MoveMilitaryLocalController::class, 'Store']);
    Route::put('update-move-military-local/{id}', [MoveMilitaryLocalController::class, 'Update']);
    Route::get('show-time-move-military-local/{id}', [MoveMilitaryLocalController::class, 'show']); //show lần cấp của từng sinh viên
});

// thong bao
Route::group([
    'prefix' => 'notification-management',
    'middleware' => ['auth:admin-api', 'scopes:admin']
], function () {
    Route::get('index-header-notification', [NotificationController::class, 'IndexHeader']); //lay ra danh sach tieu de thong bao
    Route::get('show-notification/{id}', [NotificationController::class, 'show']); //lay ra tieu de va noi dung thong bao voi $id
    Route::post('store-notification', [NotificationController::class, 'StoreNotification']);  //luu thong bao moi
    Route::put('update-notification/{id}', [NotificationController::class, 'UpdateNotification']); //update thong bao
    Route::put('update-notification-file/{id}', [NotificationController::class, 'UpdateNoti']); //update filename sau khi xoa
    Route::delete('delete-notification/{id}', [NotificationController::class, 'DestroyNotification']); //xoa 1 thong bao
    Route::post('sent-notification-students', [NotificationController::class, 'SentNotificationStudent']); //gui thong bao den sinh vien
    Route::post('post-notification-file/{id}', [NotificationController::class, 'UpdateFile']); //update file thong bao
    Route::delete('delete-notification-file/{id}', [NotificationController::class, 'DeleteFile']); //xoa file thong bao
    Route::put('update-filename/{id}/{filename}', [NotificationController::class, 'UpdateName']); //update filename sau khi uploadfile

});
