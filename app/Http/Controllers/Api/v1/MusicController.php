<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Music;
use App\Models\OAuth;
use App\Models\UserMusic;
use App\Models\Users;
use App\Models\UserSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * 音乐
 * Class MusicController
 * @package App\Http\Controllers\Api\v1
 */
class MusicController extends BaseController
{
    /**
     * @var string $rootUrl api地址
     * @var UserSearch $musicUserSearchModel 用户音乐搜索模型
     * @var Music $musicModel 音乐模型
     * @var UserMusic $userMusicModel 用户音乐模型
     * @var OAuth $oauthModel 授权模型
     * @var Users $userModel 用户模型
     */
    protected $rootUrl='https://api.imjad.cn/cloudmusic/?',$musicUserSearchModel,$musicModel,$userMusicModel,$oauthModel;

    /**
     * 构造函数
     * MusicController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->musicUserSearchModel = UserSearch::getInstance();
        $this->musicModel = Music::getInstance();
        $this->userMusicModel = UserMusic::getInstance();
        $this->oauthModel = OAuth::getInstance();
    }

    /**
     * 用户搜索
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        switch ($this->post['form']){
            case 'api':
                //获取网易歌曲参数
                $validate = Validator::make($this->post,['search_type'=>'required','s'=>'required']);
                if ($validate->fails()){
                    return $this->ajax_return(Code::ERROR,$validate->errors()->first());
                }
                //查询关键词
                $userSearchData = array('s' =>$this->post['s'], 'username' =>'vuedemo', 'ip' =>$request->ip(), 's_type' =>$this->post['search_type'], 'status' =>'1');
                //用户搜索关键词插入数据库，进行同步数据
                $searchWhere[] = array('s','=',$this->post['s']);
                $searchWhere[] = array('s_type','=',$this->post['search_type']);
                $userSearch = $this->musicUserSearchModel->getResult($searchWhere);
                if (empty($userSearch)){
                    $this->musicUserSearchModel->addResult($userSearchData);
                }else{
                    $this->musicUserSearchModel->updateResult(array('count'=>$userSearch->count+1),$searchWhere);
                }
                $data = array('type' =>'search', 'search_type' =>$this->post['search_type'], 'limit' =>$this->post['limit'], 'offset' =>$this->post['page'], 's' =>$this->post['s']);
                return $this->search($data);
                break;
            case 'mysql':
                $result = $this->musicModel->getResultLists($this->post['s']??'',$this->post['page']??1,$this->post['limit']??20);
                foreach ($result['data'] as $item){
                    $item->search_type_name = $this->searchType($item->search_type);
                    $item->form='mysql';
                }
                return $this->ajax_return(Code::SUCCESS,'successfully',$result);
                break;
            default:
                return $this->ajax_return(Code::ERROR,'error');
                break;
        }
    }

    /**
     * 获取单曲
     * @param array $data
     * @return JsonResponse
     */
    protected function search($data)
    {
        $result = $this->curl($this->rootUrl.http_build_query($data));
        $arr = [];
        if ($result['state']!=200){
            return $this->ajax_return(Code::ERROR,'request error');
        }
        $result = json_decode($result['data'],true);
        if ($result['code']!=200){
            return $this->ajax_return(Code::ERROR,$result['msg']);
        }
        switch ($data['search_type']) {
            case 1:
                $arr = $this->searchSingle($result, $data);
                break;
            case 10:
                $arr = $this->searchAlbum($result, $data);
        }
        return $this->ajax_return(Code::SUCCESS,'successfully',$arr);
    }

    /**
     * 单曲搜索
     * @param $result
     * @param $data
     * @return mixed
     */
    protected function searchSingle($result,$data)
    {
        $i = 0;
        foreach ($result['result']['songs'] as $item) {
            $i++;
            $music['music_id'] = $item['id'];
            $music['music_name'] = $item['name'];
            $music['music_time'] = sprintf("%.0f",$item['dt']/1000);
            $music['mv'] = $item['mv'];
            $music['singer_name'] = $item['ar'][0]['name'];
            $music['singer_id'] = $item['ar'][0]['id'];
            $music['pic_url'] = $item['al']['picUrl'];
            $music['s'] = $data['s'];
            $music['form'] = 'api';
            $music['search_type'] = $data['search_type'];
            $music['search_type_name'] = $this->searchType($data['search_type']);
            $music['id'] = $i;
            $arr['data'][] = $music;
        }
        $arr['total'] = $result['result']['songCount'];
        return $arr;
    }

    /**
     * 专辑搜索
     * @param $result
     * @param $data
     * @return mixed
     */
    protected function searchAlbum($result,$data)
    {
        $i = 0;
        foreach ($result['result']['albums'] as $item) {
            $i++;
            $music['music_id'] = $item['id'];  #歌单ID
            $music['music_name'] = $item['name'];
            $music['music_time'] = 0;
            $music['mv'] = '';
            $music['singer_name'] = $item['artist']['name'];
            $music['singer_id'] = $item['artist']['id'];
            $music['pic_url'] = $item['picUrl'];
            $music['s'] = $data['s'];
            $music['search_type'] = $data['search_type'];
            $music['search_type_name'] = $this->searchType($data['search_type']);
            $music['id'] = $i;
            $arr['data'][] = $music;
        }
        $arr['total'] = $result['result']['albumCount'];
        return $arr;
    }

    /**
     * 专辑歌曲获取
     * @param Request $request
     * @return JsonResponse
     */
    public function getAlbum(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $data = array('type' =>'playlist', 'id' =>$this->post['music_id']);
        $result = $this->curl($this->rootUrl.http_build_query($data));
        if ($result['state']!=200){
            return $this->ajax_return(Code::ERROR,'request error');
        }
        $result = json_decode($result['data'],true);
        if ($result['code']!=200){
            return $this->ajax_return(Code::ERROR,$result['msg']);
        }
        $i = 0;
        foreach ($result['songs'] as $item) {
            $i++;
            $music['music_id'] = $item['id'];
            $music['music_name'] = $item['name'];
            $music['music_time'] = sprintf("%.0f",$item['dt']/1000);
            $music['mv'] = $item['mv'];
            $music['singer_name'] = $item['ar'][0]['name'];
            $music['singer_id'] = $item['ar'][0]['id'];
            $music['pic_url'] = $item['al']['picUrl'];
            $music['s'] = $data['s'];
            $music['search_type'] = $data['search_type'];
            $music['search_type_name'] = $this->searchType($data['search_type']);
            $music['id'] = $i;
            $arr['data'][] = $music;
        }
        $arr['total'] = count($result['songs']);
        return $this->ajax_return(Code::SUCCESS,'successfully',$arr);
    }

    /**
     *  获取歌曲详情
     * @param Request $request
     * @return JsonResponse
     */
    public function play(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $music = $this->musicModel->getResult('music_id',$this->post['music_id']);
        if (!empty($music->music_url) && strtotime('+1 hour',strtotime($music->updated_at))>strtotime(date("Y-m-d 23:59:59")) && $this->post['form'] === 'mysql'){
            $arr['music_url'] = $music->music_url;
            $arr['lyric'] = $this->lyric($this->post['music_id'],$this->post['s']);
            return $this->ajax_return(Code::SUCCESS,'successfully',$arr);
        }
        $data = array('type' =>'song', 'id' =>$this->post['music_id']);
        $result = $this->curl($this->rootUrl.http_build_query($data));
        if ($result['state']!=200){
            return $this->ajax_return(Code::ERROR,'request error');
        }
        $result = json_decode($result['data'],true);
        if ($result['code']!=200){
            return $this->ajax_return(Code::ERROR,$result['msg']);
        }
        $arr['music_url'] = $result['data'][0]['url'];
        $this->post['br'] = $result['data'][0]['br'];
        $arr['lyric'] = $this->lyric($this->post['music_id'],$this->post['s']);
        $this->musicModel->updateResult($arr,'music_id',$this->post['music_id']);
        //用户历史表存在这个歌曲
        if ($this->userMusicModel->getResult('music_id',$this->post['music_id'])) {
            unset($arr['lyric']);
            $this->userMusicModel->updateResult($arr, 'music_id', $this->post['music_id']);
        }
        return $this->ajax_return(Code::SUCCESS,'successfully',$arr);
    }

    /**
     * 获取歌词
     * @param $id
     * @param $s
     * @return mixed|string
     */
    protected function lyric($id,$s)
    {
        $lyric='';
        $musicWhere[] = array('s','=',$s);
        $musicWhere[] = array('music_id','=',$id);
        $music = $this->musicModel->getResult($musicWhere);
        if (!empty($music->lyric)){
            $lyric = $music->lyric;
        }else{
            $data = array('type' =>'lyric', 'id' =>$id);
            $result = $this->curl($this->rootUrl.http_build_query($data));
            if ($result['state']!=200){
                return $lyric;
            }
            $result = json_decode($result['data'],true);
            if ($result['code']!=200){
                return $lyric;
            }
            if (!empty($result['lrc']) && !empty($result['lrc']['lyric'])){
                $lyric = $result['lrc']['lyric'];
            }else if (!empty($result['klyric']) && !empty($result['klyric']['lyric'])){
                $lyric = $result['klyric']['lyric'];
            }else if (!empty($result['tlyric']) && !empty($result['tlyric']['lyric'])){
                $lyric = $result['tlyric']['lyric'];
            }
        }
        return $lyric;
    }

    /**
     * 校验用户合法性
     * @param $openid
     * @return bool
     */
    protected function validateUsers($openid)
    {
        $oauth  = $this->oauthModel->getResult('openid','=',$openid);
        $users = $this->userModel->getResult('openid','=',$openid);
        if (empty($oauth) && empty($users)){
            return false;
        }
        return true;
    }

    /**
     * 单曲类型
     * @param $s_type
     * @return mixed|array
     */
    protected function searchType($s_type='')
    {
        $arr = [1 =>'单曲',10=>'专辑',100=>'歌手',1000=>'歌单',1002=>'用户',1004=>'mv',1009=>'主播电台'];
        if (!empty($s_type)){
            return $arr[$s_type];
        }
        return $arr;
    }

    /**
     * 添加用户播放历史
     * @param Request $request
     * @return JsonResponse
     */
    public function addUserMusic(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($request->post(),
            [
                'music_name'=>'required','music_id'=>'required','singer_name'=>'required',
                'music_url' =>'required|url','pic_url'=>'required|url','openid'=>'required',
                's' =>'required|string'
            ]
        );
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        if (false===$this->validateUsers($this->post['openid'])){
            return $this->ajax_return(Code::ERROR,'Permission denied');
        }
        $data = array(
            'music_name' =>$this->post['music_name'],
            'music_id' =>$this->post['music_id'],
            'singer_name' =>$this->post['singer_name'],
            'music_url' =>$this->post['music_url'],
            'pic_url' =>$this->post['pic_url'],
            'openid' =>$this->post['openid'],
            'status' =>$this->post['status']??'2',
            's' => $this->post['s'],
        );
        $where[] = array('music_id','=',$data['music_id']);
        $where[] = array('openid','=',$data['openid']);
        $userMusic = $this->userMusicModel->getResult($where);
        if (empty($userMusic)){
            $result = $this->userMusicModel->addResult($data);
        }else{
            $result = $this->userMusicModel->updateResult($data,$where);
        }
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'successfully');
        }
        return $this->ajax_return(Code::ERROR,'error');
    }

    /**
     * 播放历史
     * @param Request $request
     * @return JsonResponse
     */
    public function getHistory(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($request->post(), ['openid'=>'required'],['openid.required'=>'required params missing']);
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $result = $this->userMusicModel->getResultLists($this->post['page'],$this->post['limit'],$this->post['openid'],$this->post['status']??'');
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     * 获取搜索频率最高的词语
     * @param Request $request
     * @return JsonResponse
     */
    public function getSearch(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $result = $this->musicUserSearchModel->getSearchCount();
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     * 获取网易云音乐
     * @param $url
     * @param array $data
     * @return mixed
     */
    protected function curl($url, $data = array())
    {
        $headers = ['Accept: */*', 'appver=1.5.2;','Accept-Encoding: gzip,deflate,sdch', 'Accept-Language: zh-CN,zh;q=0.8,gl;q=0.6,zh-TW;q=0.4','Connection: keep-alive', 'Content-Type: application/x-www-form-urlencoded', 'Host: api.imjad.cn', 'Referer: https://api.imjad.cn/cloudmusic/', 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.152 Safari/537.36'];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_REFERER, 'https://api.imjad.cn/');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_ENCODING, 'application/json');
        $res = curl_exec($curl); //得到的字符串
        $state = curl_getinfo($curl, CURLINFO_HTTP_CODE); //最后一个收到的HTTP代码
        curl_close($curl);
        $re['state'] = $state;
        $re['data'] = $res;
        return $re;
    }
}
