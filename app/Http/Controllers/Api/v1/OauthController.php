<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Oauth\BaiDuController;
use App\Http\Controllers\Oauth\GiteeController;
use App\Http\Controllers\Oauth\GithubController;
use App\Http\Controllers\Oauth\OsChinaController;
use App\Http\Controllers\Oauth\QQController;
use App\Http\Controllers\Oauth\WeiBoController;
use App\Http\Controllers\Utils\Code;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
/**
 * TODO：授权列表
 * Class OauthController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class OauthController extends BaseController
{
    /**
     * TODO：授权列表
     * @param integer page
     * @param integer limit
     * @return JsonResponse
     */
    public function index()
    {
        $this->validatePost(['page'=>'required|integer|gt:0','limit'=>'required|integer']);
        $result = $this->oauthModel->getResultLists($this->post['page'],$this->post['limit'],$this->users);
        foreach ($result['data'] as &$item){
            $item->created_at = date('Y-m-d H:i:s',$item->created_at);
            $item->updated_at = date('Y-m-d H:i:s',$item->updated_at);
            $item->email = empty($item->email) ? '' :$item->email;
            $item->code = empty($item->code) ? '' :$item->code;
            $item->oauth_type = strtoupper($item->oauth_type);
        }
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }
    /**
     * TODO：更新授权用户
     * @param integer id
     * @param integer status
     * @param string act 区分修改类型
     * @return JsonResponse
     */
    public function update()
    {
        $this->validatePost(['username'=>'required|string','avatar_url'=>'required|url','email'=>'required|string|email']);
        $this->post['created_at'] = strtotime($this->post['created_at']);
        $this->post['oauth_type'] = strtolower($this->post['oauth_type']);
        $this->post['url'] = empty($this->post['url']) ? config('app.url') : $this->post['url'];
        $result = $this->oauthModel->updateResult($this->post,'id',$this->post['id']);
        if ($result){
            //修改用户表
            $users = $this->userModel->getResult('id',$this->post['uid']);
            if (!empty($users)) {
                $users->avatar_url = $this->post['avatar_url'];
                $this->userModel->updateResult(object_to_array($users),'id',$this->post['uid']);
            }
            return $this->ajax_return(Code::SUCCESS,'update oauth successfully');
        }
        return $this->ajax_return(Code::ERROR,'update oauth failed');
    }
    /**
     * TODO:账户绑定
     * @return JsonResponse
     */
    public function save()
    {
        $this->validatePost(['oauth_type'=>'required|string','remember_token'=>'required|string|size:32']);
        $result = Users::getInstance()->getResult('remember_token',$this->post['remember_token']);
        //授权账户登录没有绑定账户
        if (empty($result)) {
            return $this->ajax_return(Code::SUCCESS,'successfully',['local'=>'/admin/user/bind']);
        }
        $this->redisClient->setValue('users',json_encode($this->users),['EX' => 60]);
        switch (strtoupper($this->post['oauth_type'])) {
            case 'QQ':
                $appId = config('app.qq_appid');
                $appSecret = config('app.qq_secret');
                $QQOauth = QQController::getInstance($appId,$appSecret);
                $url = $QQOauth->getAuthUrl(16);
                $this->redisClient->setValue($QQOauth->state,$QQOauth->state,['EX' => 60]);
                break;
            case 'GITHUB':
                $appId = config('app.github_appid');
                $appSecret = config('app.github_secret');
                $gitHubOAuth = GithubController::getInstance($appId,$appSecret);
                $url = $gitHubOAuth->getAuthUrl(16);
                $this->redisClient->setValue($gitHubOAuth->state,$gitHubOAuth->state,['EX' => 60]);
                break;
            case 'GITEE':
                $appId = config('app.gitee_appid');
                $appSecret = config('app.gitee_secret');
                $giteeOAuth =GiteeController::getInstance($appId,$appSecret);
                $url = $giteeOAuth->getAuthUrl(16);
                $this->redisClient->setValue($giteeOAuth->state,$giteeOAuth->state,['EX' => 60]);
                break;
            case 'WEIBO':
                $appId = config('app.weibo_appid');
                $appSecret = config('app.weibo_secret');
                $weiboOAuth = WeiBoController::getInstance($appId,$appSecret);
                $url = $weiboOAuth->getAuthUrl(16);
                $this->redisClient->setValue($weiboOAuth->state,$weiboOAuth->state,['EX' => 60]);
                break;
            case 'BAIDU':
                $appId = config('app.baidu_appid');
                $appSecret = config('app.baidu_secret');
                $baiDuOauth = BaiDuController::getInstance($appId,$appSecret);
                $url = $baiDuOauth->getAuthUrl(16);
                $this->redisClient->setValue($baiDuOauth->state,$baiDuOauth->state,['EX' => 60]);
                break;
            case 'OSCHINA':
                $appId = config('app.os_china_appid');
                $appSecret = config('app.os_china_secret');
                $osChinaOauth = OsChinaController::getInstance($appId,$appSecret);
                $url = $osChinaOauth->getAuthUrl(16);
                $this->redisClient->setValue($osChinaOauth->state,$osChinaOauth->state,['EX' => 60]);
                break;
            default:
                $url = config('app.url');
            break;
        }
        return $this->ajax_return(Code::SUCCESS,'successfully',['oauth_url'=>$url]);
    }

    /**
     * TODO：删除授权用户
     * @param integer id
     * @return JsonResponse
     */
    public function delete()
    {
        $this->validatePost(['id'=>'required|integer']);
        $result = $this->oauthModel->deleteResult('id',$this->post['id']);
        return $result ?  $this->ajax_return(Code::SUCCESS,'remove oauth successfully') : $this->ajax_return(Code::ERROR,'remove oauth failed');
    }
}
