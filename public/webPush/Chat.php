<?php
/**
 * @author <fl140125@gmail.com>
 * Class Chat
 */
class Chat
{
    /**
     * @var Redis $redisClient
     */
    protected $redisClient;
    /**
     * @var bool $checkUserReadable
     */
    public $checkUserReadable = true;
    /**
     * @var string $hashKey
     */
    protected $hashKey = 'unread_';
    /**
     * Chat constructor.
     */
    public function __construct()
    {
        $this->redisClient = new Redis();
        $this->redisClient->connect('127.0.0.1',6379);
    }
    /**
     * TODO：缓存用户聊天记录
     * @param $from
     * @param $to
     * @param $room_id
     * @param $message
     * @return bool|int
     */
    public function setChatMsgLists($from,$to,$room_id,$message)
    {
        $value = str_replace('\\','',json_encode($message,JSON_UNESCAPED_UNICODE));
        $keyName = $to == 'all' ? 'receive_all_'.$room_id : "receive_{$from}_{$to}";
        $res = $this ->redisClient-> lPush($keyName, $value);
        //消息接受者无法立刻查看时，将消息设置为未读
        if (!$this -> checkUserReadable) {
            $this -> setUnreadMsgLists($from,$to);
        }
        return $res;
    }
    /**
     * TODO：获取聊天记录
     * @param $from
     * @param $to
     * @param $room_id
     * @return array
     */
    public function getChatMsgLists($from, $to,$room_id)
    {
        //群聊消息
        if ($to == 'all') {
            $recName = 'receive_all_'.$room_id;
            $num = $this->getMsgLen($recName);
            $recList = $this ->redisClient-> lRange($recName, 0, (int)($num));
            $messageLists = array();
            foreach ($recList as $item) {
                array_push($messageLists,json_decode($item,true));
            }
            $time = array();
            foreach ($messageLists as $item) {
                $time[] = $item['time'];
            }
            array_multisort($time,SORT_ASC,$messageLists);
            return $messageLists;
        }
        //接受的消息
        $recName =  "receive_{$from}_{$to}";
        $num = $this->getMsgLen($recName);
        $recList = $this ->redisClient-> lRange($recName, 0, (int)($num));
        //发送的消息
        $sendName = "receive_{$to}_{$from}";
        $sendLists = $this ->redisClient-> lRange($sendName, 0, (int)($num));
        $messageLists = array();
        foreach ($sendLists as $item) {
            array_push($messageLists,json_decode($item,true));
        }
        foreach ($recList as $item) {
            array_push($messageLists,json_decode($item,true));
        }
        $time = array();
        foreach ($messageLists as $item) {
            $time[] = $item['time'];
        }
        array_multisort($time,SORT_ASC,$messageLists);
        return $messageLists;
    }
    /**
     * TODO:获取聊天记录长度
     * @param $key
     * @return int
     */
    protected function getMsgLen($key)
    {
        return $this->redisClient->lLen($key);
    }
    /**
     * TODO：获取用户未读消息记录数
     * @param $to
     * @return array
     */
    public function getUnreadMsgCount($to) {
        return $this ->redisClient-> hGetAll($this->hashKey.$to);
    }
    /**
     * TODO：包括所有未读消息内容的数组
     * @param $from
     * @param $to
     * @return array
     */
    public function getUnreadMsg($from, $to)
    {
        $countArr = $this -> getUnreadMsgCount($to);
        $count = $countArr[$from];
        $keyName = "receive_{$from}_{$to}";
        return $this ->redisClient -> lRange($keyName, 0, (int)($count));
    }
    /**
     * TODO:添加用户未读消息记录数
     * @param $from
     * @param $to
     * @return int
     */
    public function setUnreadMsgLists($from,$to)
    {
        return $this->redisClient->hIncrBy($this->hashKey.$to,$from,1);
    }
    /**
     * TODO:删除未读消息
     * @param $from
     * @param $to
     * @return bool|int
     */
    public function delUnreadMsg($from,$to)
    {
        return $this-> redisClient -> hDel($this->hashKey.$to, $from);
    }
    /**
     * TODO:判断是否存在
     * @param $key
     * @param $value
     * @return bool
     */
    public function sIsMember($key,$value)
    {
        return $this->redisClient->sIsMember($key,$value);
    }
    /**
     * TODO：获取集合
     * @param $key
     * @return array
     */
    public function sMembers($key)
    {
        return $this->redisClient->SMEMBERS($key);
    }

    /**
     * TODO：图片拼接
     * @param $imgArr
     * @param $target_image
     * @param $source_img
     */
    public function saveImg($imgArr,$target_image,$source_img)
    {
        $positionArr = array();
        switch (count($imgArr)) {
            case 2:
                $positionArr = array(
                    0 => array('x' => 0,'y' => 50,'w' => 74,'h' => 50),
                    1 => array('x' => 75,'y' => 50,'w' => 74,'h' => 50)
                );
                break;
            case 3:
                $positionArr = array(
                    0 => array('x' => 0,'y' => 50,'w' => 49,'h' => 50),
                    1 => array('x' => 50,'y' => 50,'w' => 49,'h' => 50),
                    2 => array('x' => 100,'y' => 50,'w' => 49,'h' => 50)
                );
                break;
            case 4:
                $positionArr = array(
                    0 => array('x' => 0,'y' => 0,'w' => 74,'h' => 74),
                    1 => array('x' => 75,'y' => 0,'w' => 74,'h' => 74),
                    2 => array('x' => 0,'y' => 75,'w' => 74,'h' => 74),
                    3 => array('x' => 75,'y' => 75,'w' => 74,'h' => 74)
                );
                break;
            case 5:
                $positionArr = array(
                    0 => array('x' => 0,'y' => 0,'w' => 49,'h' => 74),
                    1 => array('x' => 50,'y' => 0 ,'w' => 49,'h' => 74),
                    2 => array('x' => 100,'y' => 0,'w' => 49,'h' => 74),
                    3 => array('x' => 25,'y' => 75,'w' => 49,'h' => 74),
                    4 => array('x' => 75,'y' => 75,'w' => 49,'h' => 74),
                );
                break;
            case 6:
                $positionArr = array(
                    0 => array('x' => 0,'y' => 0,'w' => 49,'h' => 74),
                    1 => array('x' => 50,'y' => 0 ,'w' => 49,'h' => 74),
                    2 => array('x' => 100,'y' => 0,'w' => 49,'h' => 74),
                    3 => array('x' => 0,'y' => 75,'w' => 49,'h' => 74),
                    4 => array('x' => 50,'y' => 75,'w' => 49,'h' => 74),
                    5 => array('x' => 100,'y' => 75,'w' => 49,'h' => 74),
                );
                break;
            case 7:
                $positionArr = array(
                    0 => array('x' => 50,'y' => 100,'w' => 49,'h' => 49),
                    1 => array('x' => 100,'y' => 50,'w' => 49,'h' => 49),
                    2 => array('x' => 50,'y' => 50,'w' => 49,'h' => 49),
                    3 => array('x' => 0,'y' => 50,'w' => 49,'h' => 49),
                    4 => array('x' => 100,'y' => 0,'w' => 49,'h' => 49),
                    5 => array('x' => 50,'y' => 0 ,'w' => 49,'h' => 49),
                    6 => array('x' => 0,'y' => 0,'w' => 49,'h' => 49)
                );
                break;
            case 8:
                $positionArr = array(
                    0 => array('x' => 0,'y' => 0,'w' => 49,'h' => 49),
                    1 => array('x' => 50,'y' => 0 ,'w' => 49,'h' => 49),
                    2 => array('x' => 100,'y' => 0,'w' => 49,'h' => 49),
                    3 => array('x' => 0,'y' => 50,'w' => 49,'h' => 49),
                    4 => array('x' => 50,'y' => 50,'w' => 49,'h' => 49),
                    5 => array('x' => 100,'y' => 50,'w' => 49,'h' => 49),
                    6 => array('x' => 25,'y' => 100,'w' => 49,'h' => 49),
                    7 => array('x' => 75,'y' => 100,'w' => 49,'h' => 49),
                );
                break;
            case 9:
                $positionArr = array(
                    0 => array('x' => 0,'y' => 0,'w' => 49,'h' => 49),
                    1 => array('x' => 50,'y' => 0 ,'w' => 49,'h' => 49),
                    2 => array('x' => 100,'y' => 0,'w' => 49,'h' => 49),
                    3 => array('x' => 0,'y' => 50,'w' => 49,'h' => 49),
                    4 => array('x' => 50,'y' => 50,'w' => 49,'h' => 49),
                    5 => array('x' => 100,'y' => 50,'w' => 49,'h' => 49),
                    6 => array('x' => 0,'y' => 100,'w' => 49,'h' => 49),
                    7 => array('x' => 50,'y' => 100,'w' => 49,'h' => 49),
                    8 => array('x' => 100,'y' => 100,'w' => 49,'h' => 49),
                );
                break;
        }
        $target_image = Imagecreatefrompng($target_image);
        foreach ($positionArr as $key=> $item) {
            $src_im = $this->ImageShrink($imgArr[$key],$item['w'],$item['h']);
            imagecopy($target_image,$src_im,$item['x'],$item['y'],0,0,$item['w'],$item['h']);
        }
        Imagepng($target_image,$source_img);
    }

    /**
     * TODO：图片等比缩放
     * @param $imgfile
     * @param $minx
     * @param $miny
     * @return false|resource
     */
    public function ImageShrink($imgfile,$minx,$miny)
    {
        //获取大图信息
        $imageSizeArr = getimagesize($imgfile);
        $maxx=$imageSizeArr[0];//宽
        $maxy=$imageSizeArr[1];//长
        //大图资源
        $maxim = '';
        switch ($imageSizeArr['mime']) {
            case 'image/png':
                $maxim = Imagecreatefrompng($imgfile);
                break;
            case 'image/jpeg':
                $maxim = Imagecreatefromjpeg($imgfile);
                break;
            case 'image/gif':
                $maxim = Imagecreatefromgif($imgfile);
                break;
        }
//        //等比缩放
//        if(($minx/$maxx)>($miny/$maxy)){
//            $scale=$miny/$maxy;
//        }else{
//            $scale=$minx/$maxx;
//        }
//        //对所求值进行取整
//        $minx=floor($maxx*$scale);
//        $miny=floor($maxy*$scale);
        //添加小图
        $minim=imagecreatetruecolor($minx,$miny);
        //缩放函数
        imagecopyresampled($minim,$maxim,0,0,0,0,$minx,$miny,$maxx,$maxy);
        return $minim;
    }
}
