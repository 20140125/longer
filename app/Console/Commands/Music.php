<?php

namespace App\Console\Commands;

use App\Models\UserSearch;
use Illuminate\Console\Command;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Music extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'music';
    /**
     * @var string $rootUrl
     */
    protected $rootUrl='https://api.imjad.cn/cloudmusic/?';
    /**
     * @var \App\Models\Music $musicModel
     */
    protected $musicModel;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'music description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->musicModel = \App\Models\Music::getInstance();
    }

    /**
     * Execute the console command.
     * @return bool
     */
    public function handle()
    {
        try{
            $limit = 20;
            $data = $this->getUserSearch(1,$limit);
            if (false === $data){
                $this->info('数据已经同步完');
                return false;
            }
            foreach ($data['data'] as $item){
                if (empty($item['music_url'])){
                    $this->warn('缺少播放链接：'.json_encode($item,JSON_UNESCAPED_UNICODE));
                    continue;
                }
                $music = $this->musicModel->getResult('music_id',$item['music_id']);
                if (empty($music)){
                    $this->musicModel->addResult($item);
                    $this->info('数据添加成功');
                    continue;
                }
                $this->musicModel->updateResult($item,'music_id',$item['music_id']);
                $this->info('数据修改成功');
                continue;
            }
            sleep(1);
            $this->info('休息1秒');
            if(!empty($data['total'])){
                $pages = ceil($data['total']/$limit);
                for($page = 2;$page<=$pages;$page++){
                    $data = $this->getUserSearch($page,$limit);
                    foreach ($data['data'] as $item){
                        if (empty($item['music_url'])){
                            $this->warn('缺少播放链接：'.json_encode($item,JSON_UNESCAPED_UNICODE));
                            continue;
                        }
                        $music = $this->musicModel->getResult('music_id',$item['music_id']);
                        if (empty($music)){
                            $this->musicModel->addResult($item);
                            $this->info('数据添加成功');
                            continue;
                        }
                        $this->musicModel->updateResult($item,'music_id',$item['music_id']);
                        $this->info('数据修改成功');
                        continue;
                    }
                    sleep(1);
                    $this->info('休息1秒');
                }
            }
        }catch (\Exception $exception){
            $this->info($exception->getMessage());
        }
        //修改搜索记录
        UserSearch::getInstance()->updateResult(array('status'=>'2','updated_at'=>date("Y-m-d H:i:s")),'id',Cache::pull('id'));
        return true;
    }

    /**
     * 单曲查询
     * @param int $page
     * @param int $limit
     * @return array|Collection|mixed
     */
    protected function getUserSearch($page,$limit)
    {
        $item = UserSearch::getInstance()->getResult('status','=','1',['s','s_type','id']);
        if (empty($item)){
            return false;
        }
        $arr = [];
        Cache::put('id',$item->id,1);
        $data = array('type' =>'search', 'search_type' =>$item->s_type, 'limit' =>$limit, 'offset' =>$page, 's' =>$item->s);
        $this->info("input params ".json_encode($data,JSON_UNESCAPED_UNICODE));
        $result = $this->curl($this->rootUrl.http_build_query($data));
        if ($result['state']!=200){
            $this->info("接口请求错误：".$this->rootUrl.http_build_query($data).' ，错误信息：'.json_encode($result,JSON_UNESCAPED_UNICODE));
            return $arr;
        }
        $result = json_decode($result['data'],true);
        if ($result['code']===200){
            foreach ($result['result']['songs'] as $row) {
                $music['music_id'] = $row['id'];
                $music['music_name'] = $row['name'];
                $music['music_time'] = sprintf("%.0f",$row['dt']/1000);
                $music['mv'] = $row['mv'];
                $music['singer_name'] = $row['ar'][0]['name'];
                $music['singer_id'] = $row['ar'][0]['id'];
                $music['pic_url'] = $row['al']['picUrl'];
                $music['s'] = $data['s'];
                $music['search_type'] = $item->s_type;
                $play = $this->play($row['id'],$data['s']);
                $music['music_url'] = $play['music_url'];
                $music['br'] = $play['br'];
                $music['lyric'] = $this->lyric($row['id'],$data['s']);
                $arr['data'][] = $music;
            }
            $arr['total'] = $result['result']['songCount'];
        }
        return $arr;
    }

    /**
     * 获取歌曲详情
     * @param $id
     * @param $s
     * @return JsonResponse
     */
    public function play($id,$s)
    {
        $musicWhere[] = array('s','=',$s);
        $musicWhere[] = array('music_id','=',$id);
        $data = array('type' =>'song', 'id' =>$id);
        $arr['music_url'] = '';
        $arr['br'] = '';
        $result = $this->curl($this->rootUrl.http_build_query($data));
        if ($result['state']!=200){
            $this->info("接口请求错误：".$this->rootUrl.http_build_query($data).' ，错误信息：'.json_encode($result,JSON_UNESCAPED_UNICODE));
            return $arr;
        }
        $result = json_decode($result['data'],true);
        if ($result['code']!=200){
            return $arr;
        }
        $arr['music_url'] = $result['data'][0]['url'];
        $arr['br'] = $result['data'][0]['br'];
        $this->info("获取歌曲链接：".json_encode($result,JSON_UNESCAPED_UNICODE));
        return $arr;
    }

    /**
     * 获取歌词
     * @param $id
     * @param $s
     * @return mixed|string
     */
    public function lyric($id,$s)
    {
        $lyric='';
        $musicWhere[] = array('s','=',$s);
        $musicWhere[] = array('music_id','=',$id);
        $music = \App\Models\Music::getInstance()->getResult($musicWhere);
        if (!empty($music->lyric)){
            $lyric = $music->lyric;
        }else{
            $data = array('type' =>'lyric', 'id' =>$id);
            $result = $this->curl($this->rootUrl.http_build_query($data));
            if ($result['state']!=200){
                $this->info("接口请求错误：".$this->rootUrl.http_build_query($data).' ，错误信息：'.json_encode($result,JSON_UNESCAPED_UNICODE));
                return $lyric;
            }
            $result = json_decode($result['data'],true);
            if ($result['code']!=200){
                $this->info(json_encode($result));
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
