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
        /* todo:发送验证码 */
        Route::match(['get','post'], 'mail/send',  [App\Http\Controllers\Api\v1\LoginController::class, 'sendMail'])->name('sendMail');
        /* todo:授权登录信息 */
        Route::match(['get','post'], 'oauth/config',  [App\Http\Controllers\Api\v1\SystemConfigController::class, 'getConfig'])->name('getConfig');
    });
    /* todo:鉴权 */
    Route::middleware('checkAuth')->group(function () {
        Route::match(['get','post'], 'check/authorized',  [App\Http\Controllers\Api\v1\LoginController::class, 'checkAuthorized'])->name('checkAuthorized');
    });
    /* todo:登录后 */
    Route::middleware('common') -> group(function() {
        /* 首页权限 */
        Route::match(['get','post'], 'common/menu',  [App\Http\Controllers\Api\v1\HomeController::class, 'getMenu'])->name('getMenu');
        Route::match(['get','post'], 'timeline/index',  [App\Http\Controllers\Api\v1\TimeLineController::class, 'getLists'])->name('getPlan');
        /* 全部用户 */
        Route::match(['get','post'], 'user/index',  [App\Http\Controllers\Api\v1\UsersController::class, 'getUsersLists'])->name('getUsersLists');
        /* 个人信息 */
        Route::match(['get','post'], 'userCenter/detail',  [App\Http\Controllers\Api\v1\UserCenterController::class, 'getUserInfo'])->name('getUserInfo');
        Route::match(['get','post'], 'userCenter/update',  [App\Http\Controllers\Api\v1\UserCenterController::class, 'updateUserInfo'])->name('updateUserInfo');
        /* 授权用户 */
        Route::match(['get','post'], 'oauth/index',  [App\Http\Controllers\Api\v1\OauthController::class, 'getOAuthLists'])->name('getOAuthLists');
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
        /* 申请权限 */
        Route::match(['get','post'], 'requestAuth/index',  [App\Http\Controllers\Api\v1\RequestAuthController::class, 'getRequestAuthLists'])->name('getRequestAuthLists');
        Route::match(['get','post'], 'requestAuth/get',  [App\Http\Controllers\Api\v1\RequestAuthController::class, 'getUserAuth'])->name('getUserAuth');
        Route::match(['get','post'], 'requestAuth/update',  [App\Http\Controllers\Api\v1\RequestAuthController::class, 'updateRequestAuth'])->name('updateRequestAuth');
        Route::match(['get','post'], 'requestAuth/save',  [App\Http\Controllers\Api\v1\RequestAuthController::class, 'saveRequestAuth'])->name('saveRequestAuth');
        /* 文件列表 */
        Route::match(['get','post'], 'file/index',  [App\Http\Controllers\Api\v1\FileController::class, 'getFileLists'])->name('getFileLists');
        Route::match(['get','post'], 'file/read',  [App\Http\Controllers\Api\v1\FileController::class, 'readFile'])->name('readFile');
        Route::match(['get','post'], 'file/update',  [App\Http\Controllers\Api\v1\FileController::class, 'updateFile'])->name('updateFile');
        Route::match(['get','post'], 'file/zip',  [App\Http\Controllers\Api\v1\FileController::class, 'gZipFile'])->name('gZipFile');
        Route::match(['get','post'], 'file/unzip',  [App\Http\Controllers\Api\v1\FileController::class, 'unGZipFile'])->name('unGZipFile');
        Route::match(['get','post'], 'file/delete',  [App\Http\Controllers\Api\v1\FileController::class, 'removeFile'])->name('removeFile');
        Route::match(['get','post'], 'file/upload',  [App\Http\Controllers\Api\v1\FileController::class, 'uploadFile'])->name('uploadFile');
        Route::match(['get','post'], 'file/chmod',  [App\Http\Controllers\Api\v1\FileController::class, 'setFileAuth'])->name('setFileAuth');
        Route::match(['get','post'], 'file/rename',  [App\Http\Controllers\Api\v1\FileController::class, 'renameFile'])->name('renameFile');
        Route::match(['get','post'], 'file/save',  [App\Http\Controllers\Api\v1\FileController::class, 'createFile'])->name('createFile');
        /* 系统配置 */
        Route::match(['get','post'], 'config/index',  [App\Http\Controllers\Api\v1\SystemConfigController::class, 'getSystemConfigLists'])->name('getSystemConfigLists');
        Route::match(['get','post'], 'config/detail',  [App\Http\Controllers\Api\v1\SystemConfigController::class, 'getSystemConfigLists'])->name('getSystemConfigLists');

        /* 站内通知 */
        Route::match(['get','post'], 'push/index',  [App\Http\Controllers\Api\v1\PushController::class, 'getPushLists'])->name('getPushLists');
        Route::match(['get','post'], 'push/save',  [App\Http\Controllers\Api\v1\PushController::class, 'savePush'])->name('savePush');
        Route::match(['get','post'], 'push/update',  [App\Http\Controllers\Api\v1\PushController::class, 'updatePush'])->name('updatePush');
        Route::match(['get','post'], 'push/delete',  [App\Http\Controllers\Api\v1\PushController::class, 'removePush'])->name('removePush');
        /* 接口分类 */
        Route::match(['get','post'], 'interface/index',  [App\Http\Controllers\Api\v1\ApiController::class, 'interfaceLists'])->name('interfaceLists');
        Route::match(['get','post'], 'interface/detail',  [App\Http\Controllers\Api\v1\ApiController::class, 'getInterface'])->name('getInterface');
        Route::match(['get','post'], 'interface/save',  [App\Http\Controllers\Api\v1\ApiController::class, 'saveInterface'])->name('saveInterface');
        Route::match(['get','post'], 'interface/update',  [App\Http\Controllers\Api\v1\ApiController::class, 'updateInterface'])->name('updateInterface');
        Route::match(['get','post'], 'interface/delete',  [App\Http\Controllers\Api\v1\ApiController::class, 'removeInterface'])->name('removeInterface');
        /* 城市管理 */
        Route::match(['get','post'], 'area/cache',  [App\Http\Controllers\Api\v1\AreaController::class, 'getCacheArea'])->name('getCacheArea');
        Route::match(['get','post'], 'area/index',  [App\Http\Controllers\Api\v1\AreaController::class, 'getAreaLists'])->name('getAreaLists');
        Route::match(['get','post'], 'area/weather',  [App\Http\Controllers\Api\v1\AreaController::class, 'getAreaWeather'])->name('getAreaWeather');
        /* 数据表管理 */
        Route::match(['get','post'], 'database/index',  [App\Http\Controllers\Api\v1\DatabaseController::class, 'getDatabaseLists'])->name('getDatabaseLists');
        Route::match(['get','post'], 'database/backup',  [App\Http\Controllers\Api\v1\DatabaseController::class, 'backUpTable'])->name('backUpTable');
        Route::match(['get','post'], 'database/optimize',  [App\Http\Controllers\Api\v1\DatabaseController::class, 'optimizeTabled'])->name('optimizeTabled');
        Route::match(['get','post'], 'database/repair',  [App\Http\Controllers\Api\v1\DatabaseController::class, 'repairTable'])->name('repairTable');
        Route::match(['get','post'], 'database/alter',  [App\Http\Controllers\Api\v1\DatabaseController::class, 'alterTable'])->name('alterTable');
    });
});
