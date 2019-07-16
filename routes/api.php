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
    Route::match(['get','post'],'user/login','v1\LoginController@index')->name('apiLogin');
    Route::match(['get','post'],'checkLogin','v1\LoginController@check')->name('checkLogin');
    Route::match(['get','post'],'logout','v1\LoginController@logout')->name('apiLogout');
    Route::match(['get','post'],'captcha','v1\LoginController@setCaptcha')->name('captcha');
    Route::match(['get','post'],'menu','v1\MenuController@getMenu')->name('menu');
    Route::match(['get','post'],'tree','v1\MenuController@tree')->name('apiTree');
    Route::match(['get','post'],'wx/login','v1\UsersController@login')->name('wxLogin');
    Route::match(['get','post'],'wx/getOpenid','v1\UsersController@getOpenId')->name('getOpenId');
    /********************************************共有权限********************************************************/

    /********************************************私有权限********************************************************/
    Route::match(['get','post'],'user/index','v1\UsersController@index');
    Route::match(['get','post'],'user/update','v1\UsersController@update');
    Route::match(['get','post'],'user/save','v1\UsersController@save');
    Route::match(['get','post'],'user/delete','v1\UsersController@delete');
    Route::match(['get','post'],'auth/index','v1\AuthController@index');
    Route::match(['get','post'],'auth/save','v1\AuthController@save');
    Route::match(['get','post'],'auth/update','v1\AuthController@update');
    Route::match(['get','post'],'auth/delete','v1\AuthController@delete');
    Route::match(['get','post'],'log/index','v1\LogController@index');
    Route::match(['get','post'],'log/save','v1\LogController@save')->name('logSave');
    Route::match(['get','post'],'log/delete','v1\LogController@delete')->name('logDelete');
    Route::match(['get','post'],'file/index','v1\FileController@index');
    Route::match(['get','post'],'file/read','v1\FileController@read');
    Route::match(['get','post'],'file/save','v1\FileController@save');
    Route::match(['get','post'],'file/gzip','v1\FileController@compression');
    Route::match(['get','post'],'file/unzip','v1\FileController@Decompression');
    Route::match(['get'],'file/download','v1\FileController@download');
    Route::match(['get','post'],'file/delete','v1\FileController@delete');
    Route::match(['get','post'],'role/index','v1\RoleController@index');
    Route::match(['get','post'],'role/update','v1\RoleController@update');
    Route::match(['get','post'],'role/save','v1\RoleController@save');
    Route::match(['get','post'],'role/delete','v1\RoleController@delete');
    Route::match(['get','post'],'database/index','v1\DatabaseController@index');
    Route::match(['get','post'],'database/backup','v1\DatabaseController@backup');
    Route::match(['get','post'],'local/index','v1\PositionController@index');
    Route::match(['get','post'],'tools/index','v1\PositionController@tools');
    Route::match(['get','post'],'article/index','v1\ArticleController@index');
    Route::match(['get','post'],'article/update','v1\ArticleController@update');
    Route::match(['get','post'],'article/delete','v1\ArticleController@delete');
    Route::match(['get','post'],'/getOpenId','v1\UsersController@getOpenId');
    Route::match(['get','post'],'/login', 'v1\UsersController@login');
    Route::match(['get','post'],'/publish', 'v1\UsersController@publish');
    Route::match(['get','post'],'/articles','v1\ArticleController@index');
    Route::match(['get','post'],'api/index','v1\ApiController@index');
    Route::match(['post','get'],'api/update','v1\ApiController@update');
    Route::match(['post','get'],'api/save','v1\ApiController@save');
    Route::match(['post','get'],'api/delete','v1\ApiController@delete');
    Route::match(['get','post'],'api/add','v1\ApiController@add');
    Route::match(['post','get'],'api/edit','v1\ApiController@edit');
    Route::match(['post','get'],'category/index','v1\ApiController@category');
    Route::match(['post','get'],'category/delete','v1\ApiController@CategoryDelete');
    Route::match(['post','get'],'music/index','v1\MusicController@index')->name('apiMusicIndex');
    Route::match(['post','get'],'music/play','v1\MusicController@play')->name('apiMusicPlay');
    Route::match(['post','get'],'music/add','v1\MusicController@addUserMusic')->name('apiMusicHistory');
    Route::match(['post','get'],'music/history','v1\MusicController@getHistory')->name('apiMusicHistoryLists');
    Route::match(['post','get'],'music/search','v1\MusicController@getSearch')->name('apiMusicSearch');

    /********************************************私有权限********************************************************/

    /******************************************第三方登陆**********************************************************/
    Route::get('oauth-login/qq','v1\OauthLoginController@QQ')->name('qqLogin');
    Route::get('callback/qq','v1\OauthCallbackController@QQ')->name('qqCallback');

    Route::get('oauth-login/github','v1\OauthLoginController@gitHub')->name('gitHubLogin');
    Route::get('callback/github','v1\OauthCallbackController@gitHub')->name('gitHubCallback');

    Route::get('oauth-login/weibo','v1\OauthLoginController@weibo')->name('weiboLogin');
    Route::get('callback/weibo','v1\OauthCallbackController@weibo')->name('weiboCallback');

    Route::get('oauth-login/gitee','v1\OauthLoginController@gitee')->name('giteeLogin');
    Route::get('callback/gitee','v1\OauthCallbackController@gitee')->name('giteeCallback');
    /******************************************第三方登陆**********************************************************/

});
