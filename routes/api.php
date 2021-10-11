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
        Route::match(['get', 'post'], 'account/login', [App\Http\Controllers\Api\v1\LoginController::class, 'login'])->name('login');
        /* todo:验证码上报 */
        Route::match(['get', 'post'], 'report/code', [App\Http\Controllers\Api\v1\LoginController::class, 'reportCode'])->name('reportCode');
        /* todo:校验登录 */
        Route::match(['get', 'post'], 'check/authorized', [App\Http\Controllers\Api\v1\LoginController::class, 'checkAuthorized'])->name('checkAuthorized');
        /* todo:发送验证码 */
        Route::match(['get', 'post'], 'mail/send', [App\Http\Controllers\Api\v1\LoginController::class, 'sendMail'])->name('sendMail');
        /* todo:授权登录信息 */
        Route::match(['get', 'post'], 'oauth/config', [App\Http\Controllers\Api\v1\SystemConfigController::class, 'getSystemConfig'])->name('getSystemConfig');
        /* todo:小程序 */
        Route::post('mini_program/login', [App\Http\Controllers\Api\MiniProgram\LoginController::class, 'login'])->name('wxLogin');
        Route::post('mini_program/openid', [App\Http\Controllers\Api\MiniProgram\LoginController::class, 'getOpenId'])->name('getOpenId');
        Route::post('image/lists', [App\Http\Controllers\Api\MiniProgram\ImageController::class, 'getImageLists'])->name('getImageLists');
        Route::post('image/newLists', [App\Http\Controllers\Api\MiniProgram\ImageController::class, 'getNewImageLists'])->name('getNewImageLists');
        Route::post('image/hotLists', [App\Http\Controllers\Api\MiniProgram\ImageController::class, 'getHotImageLists'])->name('getHotImageLists');
        Route::post('image/hotKeyWord', [App\Http\Controllers\Api\MiniProgram\ImageController::class, 'getHotKeyWords'])->name('getHotKeyWords');
    });
    /* todo:实时鉴权 */
    Route::middleware('checkAuth')->group(function () {
        Route::match(['get', 'post'], 'check/authorized', [App\Http\Controllers\Api\v1\LoginController::class, 'checkAuthorized'])->name('checkAuthorized');
    });
    /* todo:登录后鉴权 */
    Route::middleware('common')->group(function () {
        /* todo:首页权限 */
        Route::match(['get', 'post'], 'common/menu', [App\Http\Controllers\Api\v1\HomeController::class, 'getMenu'])->name('getMenu');
        Route::match(['get', 'post'], 'timeline/index', [App\Http\Controllers\Api\v1\TimeLineController::class, 'getLists'])->name('getPlan');
        /* todo:表情图 */
        Route::match(['get', 'post'], 'emotion/index', [App\Http\Controllers\Api\v1\EmotionController::class, 'getLists'])->name('getEmotion');
        /* todo:全部用户 */
        Route::match(['get', 'post'], 'users/index', [App\Http\Controllers\Api\v1\UsersController::class, 'getUsersLists'])->name('getUsersLists');
        Route::match(['get', 'post'], 'users/update', [App\Http\Controllers\Api\v1\UsersController::class, 'updateUsers'])->name('updateUsers');
        Route::match(['get', 'post'], 'users/cache', [App\Http\Controllers\Api\v1\UsersController::class, 'getCacheUserLists'])->name('getCacheUserLists');
        /* todo:个人信息 */
        Route::match(['get', 'post'], 'userCenter/index', [App\Http\Controllers\Api\v1\UserCenterController::class, 'getUserInfo'])->name('getUserInfo');
        Route::match(['get', 'post'], 'userCenter/update', [App\Http\Controllers\Api\v1\UserCenterController::class, 'updateUserInfo'])->name('updateUserInfo');
        /* todo:授权用户 */
        Route::match(['get', 'post'], 'oauth/index', [App\Http\Controllers\Api\v1\OauthController::class, 'getOAuthLists'])->name('getOAuthLists');
        Route::match(['get', 'post'], 'oauth/update', [App\Http\Controllers\Api\v1\OauthController::class, 'bindEmail'])->name('bindEmail');
        /* todo:权限管理 */
        Route::match(['get', 'post'], 'auth/index', [App\Http\Controllers\Api\v1\AuthController::class, 'getAuthLists'])->name('getAuthLists');
        Route::match(['get', 'post'], 'auth/tree', [App\Http\Controllers\Api\v1\AuthController::class, 'getAuthTree'])->name('getAuthTree');
        Route::match(['get', 'post'], 'auth/save', [App\Http\Controllers\Api\v1\AuthController::class, 'saveAuth'])->name('saveAuth');
        Route::match(['get', 'post'], 'auth/update', [App\Http\Controllers\Api\v1\AuthController::class, 'updateAuth'])->name('updateAuth');
        /* todo:日志管理 */
        Route::match(['get', 'post'], 'log/index', [App\Http\Controllers\Api\v1\LogController::class, 'getLogLists'])->name('getLogLists');
        Route::match(['get', 'post'], 'log/delete', [App\Http\Controllers\Api\v1\LogController::class, 'removeLog'])->name('removeLog');
        /* todo:角色管理 */
        Route::match(['get', 'post'], 'role/index', [App\Http\Controllers\Api\v1\RoleController::class, 'getRoleLists'])->name('getRoleLists');
        Route::match(['get', 'post'], 'role/auth', [App\Http\Controllers\Api\v1\RoleController::class, 'getRoleAuth'])->name('getRoleAuth');
        Route::match(['get', 'post'], 'role/update', [App\Http\Controllers\Api\v1\RoleController::class, 'updateRole'])->name('updateRole');
        Route::match(['get', 'post'], 'role/save', [App\Http\Controllers\Api\v1\RoleController::class, 'saveRole'])->name('saveRole');
        /* todo:申请权限 */
        Route::match(['get', 'post'], 'permission/index', [App\Http\Controllers\Api\v1\PermissionApplyController::class, 'getPermissionApplyLists'])->name('getPermissionApplyLists');
        Route::match(['get', 'post'], 'permission/get', [App\Http\Controllers\Api\v1\PermissionApplyController::class, 'getUserAuth'])->name('getUserAuth');
        Route::match(['get', 'post'], 'permission/update', [App\Http\Controllers\Api\v1\PermissionApplyController::class, 'updatePermissionApply'])->name('updatePermissionApply');
        Route::match(['get', 'post'], 'permission/save', [App\Http\Controllers\Api\v1\PermissionApplyController::class, 'savePermissionApply'])->name('savePermissionApply');
        /* todo:文件列表 */
        Route::match(['get', 'post'], 'file/index', [App\Http\Controllers\Api\v1\FileController::class, 'getFileLists'])->name('getFileLists');
        Route::match(['get', 'post'], 'file/read', [App\Http\Controllers\Api\v1\FileController::class, 'readFile'])->name('readFile');
        Route::match(['get', 'post'], 'file/update', [App\Http\Controllers\Api\v1\FileController::class, 'updateFile'])->name('updateFile');
        Route::match(['get', 'post'], 'file/zip', [App\Http\Controllers\Api\v1\FileController::class, 'gZipFile'])->name('gZipFile');
        Route::match(['get', 'post'], 'file/unzip', [App\Http\Controllers\Api\v1\FileController::class, 'unGZipFile'])->name('unGZipFile');
        Route::match(['get', 'post'], 'file/delete', [App\Http\Controllers\Api\v1\FileController::class, 'removeFile'])->name('removeFile');
        Route::match(['get', 'post'], 'file/upload', [App\Http\Controllers\Api\v1\FileController::class, 'uploadFile'])->name('uploadFile');
        Route::match(['get', 'post'], 'file/chmod', [App\Http\Controllers\Api\v1\FileController::class, 'setFileAuth'])->name('setFileAuth');
        Route::match(['get', 'post'], 'file/rename', [App\Http\Controllers\Api\v1\FileController::class, 'renameFile'])->name('renameFile');
        Route::match(['get', 'post'], 'file/save', [App\Http\Controllers\Api\v1\FileController::class, 'createFile'])->name('createFile');
        /* todo:系统配置 */
        Route::match(['get', 'post'], 'config/index', [App\Http\Controllers\Api\v1\SystemConfigController::class, 'getSystemConfigLists'])->name('getSystemConfigLists');
        Route::match(['get', 'post'], 'config/save', [App\Http\Controllers\Api\v1\SystemConfigController::class, 'saveSystemConfig'])->name('saveSystemConfig');
        Route::match(['get', 'post'], 'config/update', [App\Http\Controllers\Api\v1\SystemConfigController::class, 'updateSystemConfig'])->name('updateSystemConfig');
        /* todo:站内通知 */
        Route::match(['get', 'post'], 'push/index', [App\Http\Controllers\Api\v1\PushController::class, 'getPushLists'])->name('getPushLists');
        Route::match(['get', 'post'], 'push/save', [App\Http\Controllers\Api\v1\PushController::class, 'savePush'])->name('savePush');
        Route::match(['get', 'post'], 'push/update', [App\Http\Controllers\Api\v1\PushController::class, 'updatePush'])->name('updatePush');
        Route::match(['get', 'post'], 'push/delete', [App\Http\Controllers\Api\v1\PushController::class, 'removePush'])->name('removePush');
        /* todo:接口分类 */
        Route::match(['get', 'post'], 'interfaceCategory/index', [App\Http\Controllers\Api\v1\InterfaceCategoryController::class, 'categoryLists'])->name('categoryLists');
        Route::match(['get', 'post'], 'interfaceCategory/save', [App\Http\Controllers\Api\v1\InterfaceCategoryController::class, 'saveCategory'])->name('saveCategory');
        Route::match(['get', 'post'], 'interfaceCategory/update', [App\Http\Controllers\Api\v1\InterfaceCategoryController::class, 'updateCategory'])->name('updateCategory');
        Route::match(['get', 'post'], 'interfaceCategory/delete', [App\Http\Controllers\Api\v1\InterfaceCategoryController::class, 'removeCategory'])->name('removeCategory');
        /* todo:接口详情 */
        Route::match(['get', 'post'], 'interface/detail', [App\Http\Controllers\Api\v1\ApiController::class, 'getInterface'])->name('getInterface');
        Route::match(['get', 'post'], 'interface/save', [App\Http\Controllers\Api\v1\ApiController::class, 'saveInterface'])->name('saveInterface');
        Route::match(['get', 'post'], 'interface/update', [App\Http\Controllers\Api\v1\ApiController::class, 'updateInterface'])->name('updateInterface');
        /* todo:城市管理 */
        Route::match(['get', 'post'], 'area/cache', [App\Http\Controllers\Api\v1\AreaController::class, 'getCacheArea'])->name('getCacheArea');
        Route::match(['get', 'post'], 'area/index', [App\Http\Controllers\Api\v1\AreaController::class, 'getAreaLists'])->name('getAreaLists');
        Route::match(['get', 'post'], 'area/weather', [App\Http\Controllers\Api\v1\AreaController::class, 'getAreaWeather'])->name('getAreaWeather');
        /* todo:数据表管理 */
        Route::match(['get', 'post'], 'database/index', [App\Http\Controllers\Api\v1\DatabaseController::class, 'getDatabaseLists'])->name('getDatabaseLists');
        Route::match(['get', 'post'], 'database/backup', [App\Http\Controllers\Api\v1\DatabaseController::class, 'backUpTable'])->name('backUpTable');
        Route::match(['get', 'post'], 'database/optimize', [App\Http\Controllers\Api\v1\DatabaseController::class, 'optimizeTabled'])->name('optimizeTabled');
        Route::match(['get', 'post'], 'database/repair', [App\Http\Controllers\Api\v1\DatabaseController::class, 'repairTable'])->name('repairTable');
        Route::match(['get', 'post'], 'database/alter', [App\Http\Controllers\Api\v1\DatabaseController::class, 'alterTable'])->name('alterTable');
        /* todo:工具管理 */
        Route::match(['get', 'post'], 'tools/getAddress', [App\Http\Controllers\Api\v1\ToolsController::class, 'getAddress'])->name('getAddress');
        Route::match(['get', 'post'], 'tools/getWeather', [App\Http\Controllers\Api\v1\ToolsController::class, 'getWeather'])->name('getAddress');
        /*  todo：爬虫管理 */
        Route::match(['get', 'post'], 'spider/index', [App\Http\Controllers\Api\v1\SpiderController::class, 'getSpiderConfig'])->name('getSpiderConfig');
        Route::match(['get', 'post'], 'spider/running', [App\Http\Controllers\Api\v1\SpiderController::class, 'runningSpider'])->name('runningSpider');
    });
    /* todo:QQ授权登录 */
    Route::get('oauth/login/qq', [App\Http\Controllers\Api\v1\OauthLoginController::class, 'QQ'])->name('qqLogin');
    Route::get('callback/qq', [App\Http\Controllers\Api\v1\OauthCallbackController::class, 'QQ'])->name('qqCallback');
    /* todo:Github授权登录 */
    Route::get('oauth/login/github', [App\Http\Controllers\Api\v1\OauthLoginController::class, 'gitHub'])->name('gitHubLogin');
    Route::get('callback/github', [App\Http\Controllers\Api\v1\OauthCallbackController::class, 'gitHub'])->name('gitHubCallback');
    /* todo:WeiBo授权登录 */
    Route::get('oauth/login/weibo', [App\Http\Controllers\Api\v1\OauthLoginController::class, 'weibo'])->name('weiboLogin');
    Route::get('callback/weibo', [App\Http\Controllers\Api\v1\OauthCallbackController::class, 'weibo'])->name('weiboCallback');
    /* todo:Gitee授权登录 */
    Route::get('oauth/login/gitee', [App\Http\Controllers\Api\v1\OauthLoginController::class, 'gitee'])->name('giteeLogin');
    Route::get('callback/gitee', [App\Http\Controllers\Api\v1\OauthCallbackController::class, 'gitee'])->name('giteeCallback');
    /* todo:Baidu授权登录 */
    Route::get('oauth/login/baidu', [App\Http\Controllers\Api\v1\OauthLoginController::class, 'baidu'])->name('baiduLogin');
    Route::get('callback/baidu', [App\Http\Controllers\Api\v1\OauthCallbackController::class, 'baidu'])->name('baiduCallback');
    /* todo:osChina授权登录 */
    Route::get('oauth/login/osChina', [App\Http\Controllers\Api\v1\OauthLoginController::class, 'osChina'])->name('osChinaLogin');
    Route::get('callback/osChina', [App\Http\Controllers\Api\v1\OauthCallbackController::class, 'osChina'])->name('osChinaCallback');

    Route::get('callback/wxQrcode', [App\Http\Controllers\Api\v1\OauthCallbackController::class, 'wxQrcode'])->name('wxQrcodeCallback');
});
