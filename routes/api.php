<?php

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
Route::middleware('throttle:60,1')->namespace('Api')->prefix('v1')->group(function () {
    /* todo:登录前验证 */
    Route::middleware('checkLogin')->group(function () {
        /* todo:用户登录 */
        Route::match(['get','post'], 'common/login',  [App\Http\Controllers\Api\v1\LoginController::class, 'login'])->name('login');
        /* todo:验证码上报 */
        Route::match(['get','post'], 'report/code',  [App\Http\Controllers\Api\v1\LoginController::class, 'reportCode'])->name('reportCode');
        /* todo:校验登录 */
        Route::match(['get','post'], 'check/authorized',  [App\Http\Controllers\Api\v1\LoginController::class, 'checkAuthorized'])->name('checkAuthorized');
    });
    /* todo:鉴权 */
    Route::middleware('checkAuth')->group(function () {
        Route::match(['get','post'], 'check/authorized',  [App\Http\Controllers\Api\v1\LoginController::class, 'checkAuthorized'])->name('checkAuthorized');
    });
    /* todo:登录后 */
    Route::middleware('common') -> group(function() {
        Route::match(['get','post'], 'common/menu',  [App\Http\Controllers\Api\v1\HomeController::class, 'getMenu'])->name('getMenu');
        Route::match(['get','post'], 'timeline/index',  [App\Http\Controllers\Api\v1\TimeLineController::class, 'getLists'])->name('getPlan');
        /* 权限管理 */
        Route::match(['get','post'], 'auth/index',  [App\Http\Controllers\Api\v1\AuthController::class, 'getAuthLists'])->name('getAuthLists');
        Route::match(['get','post'], 'auth/tree',  [App\Http\Controllers\Api\v1\AuthController::class, 'getAuthTree'])->name('getAuthTree');
        Route::match(['get','post'], 'auth/save',  [App\Http\Controllers\Api\v1\AuthController::class, 'insertAuth'])->name('insertAuth');
        Route::match(['get','post'], 'auth/update',  [App\Http\Controllers\Api\v1\AuthController::class, 'updateAuth'])->name('updateAuth');
        /* 日志管理 */
        Route::match(['get','post'], 'log/index',  [App\Http\Controllers\Api\v1\LogController::class, 'getLogLists'])->name('getLogLists');
        Route::match(['get','post'], 'log/delete',  [App\Http\Controllers\Api\v1\LogController::class, 'removeLog'])->name('removeLog');
        /* 角色管理 */
        Route::match(['get','post'], 'role/index',  [App\Http\Controllers\Api\v1\RoleController::class, 'getRoleLists'])->name('getRoleLists');
        Route::match(['get','post'], 'role/auth',  [App\Http\Controllers\Api\v1\RoleController::class, 'getRoleAuth'])->name('getRoleAuth');
        Route::match(['get','post'], 'role/update',  [App\Http\Controllers\Api\v1\RoleController::class, 'updateRole'])->name('updateRole');
        Route::match(['get','post'], 'role/save',  [App\Http\Controllers\Api\v1\RoleController::class, 'saveRole'])->name('saveRole');
    });
});
