<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Api\v1\SystemConfig;
use Curl\Curl;
use Illuminate\Config\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * TODO: 用户管理
 * Class WxUsersController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class WxUsersController
{
    protected $post;
    /**
     * @var Repository|mixed 用户APPID（小程序）
     */
    protected $appid;
    /**
     * @var Repository|mixed 用户密钥（小程序）
     */
    protected $appsecret;

    /**
     * WxUsersController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        if ($request->isMethod('get')) {
            $this->setCode(Code::METHOD_ERROR, 'method not allow');
        }
        $this->appid = config('app.program_appid');
        $this->appsecret = config('app.program_secret');
        $this->post = $request->post();
    }

    /**
     * TODO: 微信获取用户的openid
     * @param string code 微信code
     * @return JsonResponse
     */
    public function getOpenId()
    {
        $this->validatePost(['code'=>'required|string']);
        $url = 'https://api.weixin.qq.com/sns/jscode2session?';
        $data = array(
            'appid' =>$this->appid,
            'secret' =>$this->appsecret,
            'js_code' =>$this->post['code'],
            'grant_type' =>'authorization_code'
        );
        $curl = new Curl();
        $response = $curl->post($url.http_build_query($data));
        $parsedData = json_decode(trim($response), true, 512, JSON_OBJECT_AS_ARRAY);
        return $this->ajaxReturn(200, 'successfully', $parsedData);
    }
    /**
     * TODO: 微信登陆信息
     * @return JsonResponse
     */
    public function login()
    {
        $this->validatePost([
            'nickName'=>'required|string',
            'avatarUrl'=>'required|string',
            'code2Session'=>'required|Array'
        ]);
        $oauth = [
            'username' => $this->post['nickName'],
            'openid' => $this->post['code2Session']['openid'] ?? 0,
            'access_token' => $this->post['code2Session']['session_key'] ?? 0,
            'expires' => time() + $this->post['code2Session']['expires_in'] ?? 0,
            'role_id' => 2,
            'created_at' => time(),
            'updated_at' => time(),
            'remember_token' => encrypt(md5($this->post['nickName']).time()),
            'oauth_type' => 'weixin',
            'avatar_url' => $this->post['avatarUrl']
        ];
        $where[] = array('openid', '=', $oauth['openid']);
        $where[] = array('oauth_type', '=', 'weixin');
        $oauthRes = \App\Models\Api\v1\Oauth::getInstance()->getOne($where);
        if (empty($oauthRes)) {
            $result = \App\Models\Api\v1\Oauth::getInstance()->saveOne($oauth);
            if (!empty($result)) {
                Artisan::call("longer:sync-oauth {$oauth['remember_token']}");
                return $this->ajaxReturn(200, 'login successfully', $oauth);
            }
            return  $this->ajaxReturn(201, 'login failed');
        }
        // 用户更新时间跟当前时间作对比
        if ($oauthRes->updated_at + 3600 * 24 > time()) {
            $oauthRes->remember_token = encrypt($oauthRes->remember_token);
            \App\Models\Api\v1\Oauth::getInstance()->updateOne(['id' => $oauthRes->id], (array)$oauthRes);
        }
        return $this->ajaxReturn(200, 'login successfully', (array)$oauthRes);
    }
    /**
     * todo:获取图片信息
     * @return JsonResponse
     */
    public function getImageDetail()
    {
        $this->validatePost(['type'=>'required|integer']);
        $type = DB::table('os_soogif_type')->where('id', '=', ($this->post['type']))->first(['name']);
        $result['data'] = DB::table('os_soogif')->where('type', '=', ($this->post['type']))->limit(100)->orWhere('name', '=', $type->name ?? '')->get();
        return !empty($result) ? $this->ajaxReturn(200, 'successfully', $result) :
            $this->ajaxReturn(201, 'failed');
    }

    /**
     * todo:获取最新的图片
     * @return JsonResponse
     */
    public function getNewImageBed()
    {
        $this->validatePost(['page'=>'required|integer', 'limit'=>'required|integer', 'source' => 'required|string|in:app,mini_program']);
        $where[] = ['type', '>', 105];
        if (!empty($this->post['name'])) {
            if ($this->post['source'] === 'mini_program') {
                $defaultShowImage = SystemConfig::getInstance()->getOne(['name' => 'ImageBed'], ['children'])->children;
                if (!in_array($this->post['name'], explode(',', json_decode($defaultShowImage)[1]->value)) && empty($this->post['token'])) {
                    return $this->ajaxReturn(201, 'Please Login System');
                }
                if (!empty($this->post['token'])) {
                    if (empty(\App\Models\Api\v1\Oauth::getInstance()->getOne(['remember_token' => $this->post['token']]))) {
                        return $this->ajaxReturn(201, 'Please Login System');
                    }
                }
            }
            $where = [];
            $where[] = ['name','like','%'.$this->post['name'].'%' ?? ''];
            $lists['data'] = DB::table('os_soogif')->where($where)->limit($this->post['limit'])->offset($this->post['limit'] * ($this->post['page'] - 1))->get();
            $lists['total'] =  DB::table('os_soogif')->where($where) ->count();
            return $this->ajaxReturn(200, 'successfully', $lists);
        }
        $lists['data'] = DB::table('os_soogif')->where($where)->limit($this->post['limit'])->orderByRaw('rand()')->offset($this->post['limit'] * ($this->post['page'] - 1))->get();
        $lists['total'] =  DB::table('os_soogif')->where($where) ->count();
        return $this->ajaxReturn(200, 'successfully', $lists);
    }
    /**
     * TODO:：图床列表
     * @param integer id
     * @return JsonResponse
     */
    public function imageBed()
    {
        if (empty($this->post['id'])) {
            $lists = DB::table('os_soogif_type')->where('pid', '=', $this->post['pid'] ?? 0)
                ->where('id', '<>', 105)
                ->get(['name','id','pid']);
            return $this->ajaxReturn(200, 'successfully', (array)$lists);
        }
        $validate = Validator::make(
            $this->post,
            [
                'id'=>'required|integer',
                'page'=>'required|integer',
                'limit'=>'integer|integer',
                'source' => 'required|string|in:app,mini_program'
            ]
        );
        if ($validate->fails()) {
            return $this->ajaxReturn(201, $validate->errors()->first());
        }
        $this->post['source'] = $this->post['source'] ?? 'app';
        if ($this->post['source'] === 'mini_program') {
            //判断用户是否是登录用户
            $res = DB::table('os_soogif_type')->where('id', '=', $this->post['id'])->first(['pid']);
            $defaultShowImage = SystemConfig::getInstance()->getOne(['name' => 'ImageBed'], ['children'])->children;
            if (!in_array($res->pid, explode(',', json_decode($defaultShowImage)[0]->value))
                && empty($this->post['token'])) {
                return $this->ajaxReturn(201, 'Please Login System');
            }
            if (!empty($this->post['token'])) {
                if (empty(\App\Models\Api\v1\Oauth::getInstance()->getOne(['remember_token' => $this->post['token']]))) {
                    return $this->ajaxReturn(201, 'Please Login System');
                }
            }
        }
        $res = Cache::get($this->post['page'].'_wx_'.$this->post['id']);
        if (empty($res['data'])) {
            $res = DB::table('os_soogif')
                ->where('type', '=', $this->post['id'])
                ->limit($this->post['limit'])
                ->offset($this->post['limit'] * ($this->post['page'] - 1))
                ->get();
            Cache::forever($this->post['page'].'_wx_'.$this->post['id'], $res);
        }
        $lists['data'] = $res;
        $lists['total'] =  DB::table('os_soogif')->where('type', '=', $this->post['id'])->count();
        return $this->ajaxReturn(200, 'successfully', $lists);
    }
    /**
     * todo:获取热搜关键词
     * @return JsonResponse
     */
    public function hotKeWord()
    {
        $num = $this->post['num'] ?? 1;
        $hotKeyWord = SystemConfig::getInstance()->getOne(['name' => 'ImageBed'], ['children'])->children;
        return $this->ajaxReturn(200, 'successfully', explode(',', json_decode($hotKeyWord)[$num]->value));
    }

    /**
     * todo:图片收藏
     * @return JsonResponse
     */
    public function collect()
    {
        $this->validatePost(['token'=>'required|string','post'=>'required|array','act'=>'required|integer']);
        $users = \App\Models\Api\v1\Oauth::getInstance()->getOne(['remember_token' => $this->post['token']]);
        if (empty($users)) {
            return $this->ajaxReturn(201, 'Please Login System');
        }
        $data = array(
            'image_id' => $this->post['post']['id'],
            'href' => $this->post['post']['href'],
            'name' => $this->post['post']['name'],
            'user_id' => $users->id,
            'time' => time(),
            'status' => $this->post['act']
        );
        $result = DB::table('os_collect')->where([['user_id','=',$data['user_id']],['image_id','=',$data['image_id']]])
            ->first();
        $res = empty($result) ? DB::table('os_collect')->insert($data) : DB::table('os_collect')->where('id', '=', $result->id)->update($data);
        return !empty($res) ? $this->ajaxReturn(200, 'successfully') : $this->ajaxReturn(201, 'failed');
    }
    /**
     * TODO:数据返回
     * @param $code
     * @param $msg
     * @param array $data
     * @return JsonResponse
     */
    protected function ajaxReturn($code, $msg, array $data = array())
    {
        $item = array(
            'code' =>$code,
            'msg' =>$msg,
            'item' =>$data,
        );
        return response()->json($item, $code);
    }

    /**
     * TODO：数据检验
     * @param array $rules
     * @param array $message
     */
    protected function validatePost(array $rules, array $message = [])
    {
        $validate = Validator::make($this->post, $rules, $message);
        if ($validate->fails()) {
            if ($validate->errors()->first() == 'Permission denied') {
                $this->setCode(Code::FORBIDDEN, $validate->errors()->first());
            }
            $this->setCode(201, $validate->errors()->first());
        }
    }
    /**
     * TODO:设置code
     * @param $code
     * @param $message
     */
    protected function setCode($code, $message)
    {
        setCode($code);
        exit(json_encode(array('code'=>$code,'msg'=>$message)));
    }
}
