<?php


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
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->namespace('Api')->prefix('v1')->group(function () {
    /********************************************共有权限********************************************************/
    Route::match(['get','post'], 'common/report', 'v1\LoginController@setVerifyCode')->name('reportCode');
    Route::match(['get','post'], 'common/login', 'v1\LoginController@index')->name('apiLogin');
    Route::match(['get','post'], 'common/config', 'v1\LoginController@config')->name('getConfig');
    Route::match(['get','post'], 'common/download', 'v1\LoginController@download')->name('downloadFile');
    Route::match(['get','post'], 'common/send_email', 'v1\LoginController@email')->name('sendEmail');
    Route::match(['get','post'], 'common/check_code', 'v1\LoginController@code')->name('checkCode');
    Route::match(['get','post'], 'common/check_login', 'v1\MenuController@check')->name('checkLogin');
    Route::match(['get','post'], 'common/logout', 'v1\MenuController@logout')->name('apiLogout');
    Route::match(['get','post'], 'common/menu', 'v1\MenuController@getMenu')->name('menu');
    Route::match(['get','post'], 'common/chat', 'v1\MenuController@chatMessage')->name('chatMessage');
    Route::match(['get','post'], 'common/get_city_name', 'v1\MenuController@getCityName')->name('getCityName');
    Route::match(['get','post'], 'common/reset_pass', 'v1\ResetPasswordController@resetPass')->name('resetPass');
    Route::match(['get','post'], 'common/log_save', 'v1\LogController@save')->name('logSave');
    Route::match(['get','post'], 'common/area', 'v1\AreaController@lists')->name('areaLists');
    Route::match(['get','post'], 'common/upload', 'v1\WxUsersController@upload');
    Route::match(['post','get'], 'common/emotion', 'v1\EmotionController@index')->name('emotion');
    Route::match(['post','get'], 'common/download', 'v1\LoginController@download')->name('download');
    Route::match(['get','post'], 'common/image_bed', 'v1\LoginController@bed')->name('sooGif');
    //小程序
    Route::match(['get','post'], 'wx/login', 'v1\WxUsersController@login')->name('wxLogin');
    Route::match(['get','post'], 'wx/openid', 'v1\WxUsersController@getOpenId')->name('getOpenId');
    Route::match(['get','post'], 'wx/image/bed', 'v1\WxUsersController@imageBed')->name('imageBed');
    Route::match(['get','post'], 'wx/image/details', 'v1\WxUsersController@getImageDetail')->name('getImageDetail');
    Route::match(['get','post'], 'wx/image/keyword', 'v1\WxUsersController@hotKeWord')->name('hotKeWord');
    Route::match(['get','post'], 'wx/image/new', 'v1\WxUsersController@getNewImageBed')->name('getNewImageBed');
    Route::match(['get','post'], 'wx/image/collect', 'v1\WxUsersController@collect')->name('collect');


    /********************************************共有权限********************************************************/

    /********************************************私有权限********************************************************/
    /* 管理员 */
    Route::match(['get','post'], 'user/index', 'v1\UsersController@index');
    Route::match(['get','post'], 'user/update', 'v1\UsersController@update')->name('userUpdate');
    Route::match(['get','post'], 'user/save', 'v1\UsersController@save');
    Route::match(['get','post'], 'user/bind', 'v1\UsersController@getBindInfo')->name('getBindInfo');
    Route::match(['get','post'], 'user/delete', 'v1\UsersController@delete');
    Route::match(['get','post'], 'center/index', 'v1\UsersController@center')->name('userCenter');
    Route::match(['get','post'], 'center/save', 'v1\UsersController@saveCenter')->name('saveCenter');
    /* 权限 */
    Route::match(['get','post'], 'auth/index', 'v1\AuthController@index');
    Route::match(['get','post'], 'auth/save', 'v1\AuthController@save');
    Route::match(['get','post'], 'auth/update', 'v1\AuthController@update');
    Route::match(['get','post'], 'auth/delete', 'v1\AuthController@delete');
    /* 授权 */
    Route::match(['get','post'], 'oauth/index', 'v1\OauthController@index');
    Route::match(['get','post'], 'oauth/update', 'v1\OauthController@update');
    Route::match(['get','post'], 'oauth/delete', 'v1\OauthController@delete');
    Route::match(['get','post'], 'oauth/save', 'v1\OauthController@save');
    /* 请求授权 */
    Route::match(['get','post'], 'req_rule/index', 'v1\ReqRuleController@index');
    Route::match(['get','post'], 'req_rule/save', 'v1\ReqRuleController@save')->name('reqRuleSave');
    Route::match(['get','post'], 'req_rule/update', 'v1\ReqRuleController@update');
    Route::match(['get','post'], 'req_rule/delete', 'v1\ReqRuleController@delete');
    Route::match(['get','post'], 'req_rule/get', 'v1\ReqRuleController@getAuth');
    /* 日志 */
    Route::match(['get','post'], 'log/index', 'v1\LogController@index');
    Route::match(['get','post'], 'log/delete', 'v1\LogController@delete')->name('logDelete');
    /* 配置 */
    Route::match(['get','post'], 'config/index', 'v1\ConfigController@index');
    Route::match(['get','post'], 'config/save', 'v1\ConfigController@save');
    Route::match(['get','post'], 'config/update', 'v1\ConfigController@update');
    Route::match(['get','post'], 'config/delete', 'v1\ConfigController@delete');
    /* 文件 */
    Route::match(['get','post'], 'file/index', 'v1\FileController@index');
    Route::match(['get','post'], 'file/read', 'v1\FileController@read');
    Route::match(['get','post'], 'file/save', 'v1\FileController@save');
    Route::match(['get','post'], 'file/update', 'v1\FileController@update');
    Route::match(['get','post'], 'file/rename', 'v1\FileController@rename');
    Route::match(['get','post'], 'file/chmod', 'v1\FileController@auth');
    Route::match(['get','post'], 'file/zip', 'v1\FileController@compression');
    Route::match(['get','post'], 'file/unzip', 'v1\FileController@Decompression');
    Route::match(['get','post'], 'file/upload', 'v1\FileController@upload')->name('uploadFile');
    Route::match(['get','post'], 'file/delete', 'v1\FileController@delete');
    /* 角色 */
    Route::match(['get','post'], 'role/index', 'v1\RoleController@index');
    Route::match(['get','post'], 'role/update', 'v1\RoleController@update');
    Route::match(['get','post'], 'role/save', 'v1\RoleController@save');
    Route::match(['get','post'], 'role/delete', 'v1\RoleController@delete');
    /* 数据库 */
    Route::match(['get','post'], 'database/index', 'v1\DatabaseController@index');
    Route::match(['get','post'], 'database/backup', 'v1\DatabaseController@backup');
    Route::match(['get','post'], 'database/repair', 'v1\DatabaseController@repair');
    Route::match(['get','post'], 'database/optimize', 'v1\DatabaseController@optimize');
    Route::match(['get','post'], 'database/comment', 'v1\DatabaseController@comment');
    /* 城市 */
    Route::match(['get','post'], 'area/index', 'v1\AreaController@index');
    Route::match(['get','post'], 'area/weather', 'v1\AreaController@weather');
    /* 站内信息推送 */
    Route::match(['get','post'], 'push/index', 'v1\PushController@index');
    Route::match(['get','post'], 'push/save', 'v1\PushController@save');
    Route::match(['get','post'], 'push/update', 'v1\PushController@update');
    Route::match(['get','post'], 'push/delete', 'v1\PushController@delete');
    Route::match(['get','post'], 'push/see', 'v1\PushController@read');
    /* API接口 */
    Route::match(['get','post'], 'api/index', 'v1\ApiController@index');
    Route::match(['post','get'], 'api/update', 'v1\ApiController@update');
    Route::match(['post','get'], 'api/save', 'v1\ApiController@save');
    /* API接口 */
    Route::match(['get','post'], 'api_doc/index', 'v1\ApiDocController@index');
    Route::match(['post','get'], 'api_doc/update', 'v1\ApiDocController@update');
    Route::match(['post','get'], 'api_doc/save', 'v1\ApiDocController@save');
    /* API接口分类 */
    Route::match(['post','get'], 'category/index', 'v1\ApiController@category');
    Route::match(['post','get'], 'category/delete', 'v1\ApiController@CategoryDelete');
    Route::match(['post','get'], 'category/save', 'v1\ApiController@categorySave');
    Route::match(['post','get'], 'category/update', 'v1\ApiController@CategoryUpdate');
    /* excel导入导出 */
    Route::match(['post','get'], 'excel/import', 'v1\ExcelController@import');
    Route::match(['post','get'], 'excel/export', 'v1\ExcelController@export')->name('export');
    /* 组件 */
    Route::match(['post','get'], 'components/table', 'v1\TableComponentController@table');
    Route::match(['post','get'], 'components/actions', 'v1\TableComponentController@action');
    /* 时间线 */
    Route::match(['post','get'], 'timeline/index', 'v1\TimeLineController@index');
    Route::match(['post','get'], 'timeline/save', 'v1\TimeLineController@save');
    Route::match(['post','get'], 'timeline/update', 'v1\TimeLineController@update');
    /********************************************私有权限********************************************************/

    /******************************************第三方登陆**********************************************************/
    /* QQ */
    Route::get('oauth_login/qq', 'v1\OauthLoginController@QQ')->name('qqLogin');
    Route::get('callback/qq', 'v1\OauthCallbackController@QQ')->name('qqCallback');
    /* Github */
    Route::get('oauth_login/github', 'v1\OauthLoginController@gitHub')->name('gitHubLogin');
    Route::get('callback/github', 'v1\OauthCallbackController@gitHub')->name('gitHubCallback');
    /* WeiBo */
    Route::get('oauth_login/weibo', 'v1\OauthLoginController@weibo')->name('weiboLogin');
    Route::get('callback/weibo', 'v1\OauthCallbackController@weibo')->name('weiboCallback');
    /* Gitee */
    Route::get('oauth_login/gitee', 'v1\OauthLoginController@gitee')->name('giteeLogin');
    Route::get('callback/gitee', 'v1\OauthCallbackController@gitee')->name('giteeCallback');
    /* Baidu */
    Route::get('oauth_login/baidu', 'v1\OauthLoginController@baidu')->name('baiduLogin');
    Route::get('callback/baidu', 'v1\OauthCallbackController@baidu')->name('baiduCallback');
    /* osChina */
    Route::get('oauth_login/os_china', 'v1\OauthLoginController@osChina')->name('osChinaLogin');
    Route::get('callback/os_china', 'v1\OauthCallbackController@osChina')->name('osChinaCallback');
    /******************************************第三方登陆**********************************************************/
});
