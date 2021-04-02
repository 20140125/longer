<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Config;
use App\Models\OAuth;
use App\Models\Users;
use Curl\Curl;
use Illuminate\Config\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
     * @var Config $configModel
     */
    protected $configModel;
    /**
     * @var Users $userModel
     */
    protected $userModel;

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
        $this->configModel = Config::getInstance();
        $this->userModel = Users::getInstance();
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
        return $this->ajaxReturn(Code::SUCCESS, 'successfully', $parsedData);
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
            'remember_token' => md5(md5($this->post['nickName']).time()),
            'oauth_type' => 'weixin',
            'avatar_url' => $this->post['avatarUrl']
        ];
        $where[] = array('openid','=',$oauth['openid']);
        $where[] = array('oauth_type','=','weixin');
        $oauthRes = OAuth::getInstance()->getResult($where);
        if (empty($oauthRes)) {
            $result = OAuth::getInstance()->addResult($oauth);
            if (!empty($result)) {
                Artisan::call("longer:sync-oauth {$oauth['remember_token']}");
                return $this->ajaxReturn(Code::SUCCESS, 'login successfully', $oauth);
            }
            return  $this->ajaxReturn(Code::ERROR, 'login failed');
        }
        // 用户更新时间跟当前时间作对比
        if ($oauthRes->updated_at + 3600 * 24 > time()) {
            $oauthRes->remember_token = md5($oauthRes->remember_token);
            OAuth::getInstance()->updateResult(objectToArray($oauthRes), 'id', $oauthRes->id);
        }
        return $this->ajaxReturn(Code::SUCCESS, 'login successfully', $oauthRes);
    }
    /**
     * todo:获取图片信息
     * @return JsonResponse
     */
    public function getImageDetail()
    {
        $this->validatePost(['type'=>'required|integer','id'=>'required|integer']);
        $type = DB::table('os_soogif_type')->where('id', '=', ($this->post['type']))->first(['name']);
        $result['data'] = DB::table('os_soogif')->where('type', '=', ($this->post['type']))->limit(100)->orWhere('name', '=', $type->name ?? '')->get();
        return !empty($result) ? $this->ajaxReturn(Code::SUCCESS, 'successfully', $result) :
            $this->ajaxReturn(Code::ERROR, 'failed');
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
                $defaultShowImage = $this->configModel->getResult('name', 'ImageBed', '=', ['children'])->children;
                if (!in_array($this->post['name'], explode(',', json_decode($defaultShowImage)[1]->value))
                    && empty($this->post['token'])) {
                    return ajaxReturn(Code::ERROR, 'Please Login System');
                }
                if (!empty($this->post['token'])) {
                    if (empty(OAuth::getInstance()->getResult('remember_token', $this->post['token']))) {
                        return ajaxReturn(Code::ERROR, 'Please Login System');
                    }
                }
            }
            $where = [];
            $where[] = ['name','like','%'.$this->post['name'].'%' ?? ''];
            $lists['data'] = DB::table('os_soogif')->where($where)->limit($this->post['limit'])->offset($this->post['limit'] * ($this->post['page'] - 1))->get();
            $lists['total'] =  DB::table('os_soogif')->where($where) ->count();
            return ajaxReturn(Code::SUCCESS, 'successfully', $lists);
        }
        $lists['data'] = DB::table('os_soogif')->where($where)->limit($this->post['limit'])->orderByRaw('rand()')
            ->offset($this->post['limit'] * ($this->post['page'] - 1))->get();
        $lists['total'] =  DB::table('os_soogif')->where($where) ->count();
        return ajaxReturn(Code::SUCCESS, 'successfully', $lists);
    }
    /**
     * TODO:：图床列表
     * @param integer id
     * @return JsonResponse
     */
    public function imageBed()
    {
        if (empty($this->post['id'])) {
            $lists = $lists = DB::table('os_soogif_type')->where('pid', '=', $this->post['pid'] ?? 0)
                ->where('id', '<>', 105)
                ->get(['name','id','pid']);
            return ajaxReturn(Code::SUCCESS, 'successfully', $lists);
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
            return ajaxReturn(Code::ERROR, $validate->errors()->first());
        }
        $this->post['source'] = $this->post['source'] ?? 'app';
        if ($this->post['source'] === 'mini_program') {
            //判断用户是否是登录用户
            $res = DB::table('os_soogif_type')->where('id', '=', $this->post['id'])->first(['pid']);
            $defaultShowImage = $this->configModel->getResult('name', 'ImageBed', '=', ['children'])->children;
            if (!in_array($res->pid, explode(',', json_decode($defaultShowImage)[0]->value))
                && empty($this->post['token'])) {
                return ajaxReturn(Code::ERROR, 'Please Login System');
            }
            if (!empty($this->post['token'])) {
                if (empty(OAuth::getInstance()->getResult('remember_token', $this->post['token']))) {
                    return ajaxReturn(Code::ERROR, 'Please Login System');
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
        return ajaxReturn(Code::SUCCESS, 'successfully', $lists);
    }
    /**
     * todo:获取热搜关键词
     * @return JsonResponse
     */
    public function hotKeWord()
    {
        $num = $this->post['num'] ?? 1;
        $hotKeyWord = $this->configModel->getResult('name', 'ImageBed', '=', ['children'])->children;
        return ajaxReturn(Code::SUCCESS, 'successfully', explode(',', json_decode($hotKeyWord)[$num]->value));
    }

    /**
     * todo:图片收藏
     * @return JsonResponse
     */
    public function collect()
    {
        $this->validatePost(['token'=>'required|string','post'=>'required|array','act'=>'required|integer']);
        $users = OAuth::getInstance()->getResult('remember_token', $this->post['token']);
        if (empty($users)) {
            return ajaxReturn(Code::ERROR, 'Please Login System');
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
        $res = empty($result) ? DB::table('os_collect')->insert($data) :
            DB::table('os_collect')->where('id', '=', $result->id)->update($data);
        return !empty($res) ? $this->ajaxReturn(Code::SUCCESS, 'successfully') :
            $this->ajaxReturn(Code::ERROR, 'failed');
    }
    /**
     * TODO:数据返回
     * @param $code
     * @param $msg
     * @param array $data
     * @return JsonResponse
     */
    protected function ajaxReturn($code, $msg, $data = array())
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
                $this->setCode(Code::NOT_ALLOW, $validate->errors()->first());
            }
            $this->setCode(Code::ERROR, $validate->errors()->first());
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

    /**
     * TODO:：文件上传
     * @param Request $request (file:文件资源,rand:是否随机,path:文件路径)
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');
        if ($file->isValid()) {
            //获取文件的扩展名
            $ext = $file->getClientOriginalExtension();
            //获取文件的绝对路径
            $path = $file->getRealPath();
            //图片格式上传错误
            switch (strtolower($ext)) {
                case 'jpg':
                case 'gif':
                case 'png':
                case 'jpeg':
                    //图片大小上传错误
                    if ($file->getSize()>2*1024*1024) {
                        return $this->ajaxReturn(Code::ERROR, 'upload image size error');
                    }
                    break;
                case 'mp4':
                    if ($file->getSize()>5*1024*1024) {
                        return $this->ajaxReturn(Code::ERROR, 'upload video size error');
                    }
                    break;
                default:
                    return $this->ajaxReturn(Code::ERROR, 'Unsupported file format');
            }
            $filename = date('Ymd')."/".md5(date('YmdHis')).uniqid().".".$ext;
            Storage::disk('public')->put($filename, file_get_contents($path));
            $info = array(
                'username' => $this->post['username'] ?? 'tourist',
                'href' => '/v1/wx/upload',
                'msg' => 'upload file '.$file->getClientOriginalName().' successfully'
            );
            actLog($info);
            return $this->ajaxReturn(
                Code::SUCCESS,
                'upload file '.$file->getClientOriginalName().' successfully',
                array('src'=>config('app.url').'storage/'.$filename)
            );
        }
        return $this->ajaxReturn(Code::ERROR, 'upload file failed');
    }
}
