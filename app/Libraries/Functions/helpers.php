<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 2018/10/13
 * Time: 11:16
 */

use App\Models\Log;

if (!function_exists('ajax_return'))
{
    /**
     * 返回JSON数据
     * @param $code
     * @param $msg
     * @param string $url
     * @param array $data
     * @return \Illuminate\Http\JsonResponse
     */
    function ajax_return ($code,$msg,$url='',$data = [])
    {
        $item = array(
            'code' =>$code,
            'msg' =>$msg,
            'url' =>$url??request()->getRequestUri(),
            'item' =>$data,
        );
        return response()->json($item,$code);
    }
}

if (!function_exists('get_round_num'))
{
    /**
     * 生成随机字符串
     * @param int $length
     * @return string
     */
    function get_round_num($length=8)
    {
        static $str = '0123456789qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPLKJHGFDSAZXCVBNM';
        return mb_substr(str_shuffle($str),0,$length);
    }
}
if (!function_exists('find_str'))
{
    /**
     * 寻找字符串出现的位置
     * @param $str
     * @param $chr
     * @param $num
     * @return bool|int
     */
    function find_str($str,$chr,$num)
    {
        $first = strpos($str,$chr,$num);
        for ($i=0;$i<$num;$i++){
            $first = strpos($str,$chr,$first+1);
        }
        return $first;
    }
}

if (!function_exists('get_tree'))
{
    /**
     * 树形结构
     * @param $data
     * @param $pid
     * @param $attr
     * @return array
     */
    function get_tree($data,$pid,$attr = 'data')
    {
        $tree = array();
        foreach ($data as $key=> $item){
            $res = object_to_array($data[$key]);
            if ($res['pid'] == $pid){
                $res[$attr] = get_tree($data,$res['id'],$attr);
                $tree[] = $res;
            }
        }
        return $tree;
    }
}
if (!function_exists('object_to_array'))
{
    /**
     * 对象转数组
     * @param $obj
     * @return array
     */
    function object_to_array($obj) {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)object_to_array($v);
            }
        }
        return $obj;
    }
}

if (!function_exists('array_to_object'))
{
    /**
     * 数组 转 对象
     * @param array $arr 数组
     * @return object
     */
    function array_to_object($arr) {
        if (gettype($arr) != 'array') {
            return;
        }
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' || gettype($v) == 'object') {
                $arr[$k] = (object)array_to_object($v);
            }
        }
        return (object)$arr;
    }
}
if (!function_exists('set_code'))
{
    /**
     * 发送HTTP状态
     * @param integer $code 状态码
     * @return void
     */
    function set_code($code) {
        static $_status = array(
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found', // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
        );
        if(isset($_status[$code])) {
            header('HTTP/1.1 '.$code.' '.$_status[$code]);
        }
    }
}
if (!function_exists('format_bates')){
    /**
     * 获取文件大小
     * @param String $size
     * @param string $delimiter 分割符
     * @return string
     */
    function format_bytes($size, $delimiter = '')
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
        return round($size, 2) . $delimiter . $units[$i];
    }
}

if (!function_exists('file_lists'))
{
    /**
     * todo：获取文件列表
     * @param $filePath
     * @return array
     */
    function file_lists($filePath)
    {
        $fileArr = array();
        $openDir = opendir($filePath);
        while ($file = readdir($openDir)){
            if (!in_array($file,['.','..','vendor','.gitattributes','.git','.gitignore','.env','.env.example','.idea','.editorconfig','.DS_Store','node_modules','.styleci.yml','public'])){
                $fileArr[] = array(
                    'label'=>$file,
                    'fileType' => filetype($filePath.$file),
                    'children' =>[],
                    'path' =>filetype($filePath.$file) == 'dir' ? $filePath.$file.'/' : $filePath.$file,
                    'size' =>md5($filePath.$file),
                    'auth' => file_chmod($filePath.$file)
                );
            }
        }
        foreach ($fileArr as &$item){
            if ($item['fileType'] === 'dir'){
                $item['children'] = file_lists($item['path']);
            }
        }
        return $fileArr;
    }
}
if (!function_exists('file_path'))
{
    /**
     * 文件路径
     * @param $path
     * @param $basename
     * @return mixed
     */
    function file_path($path,$basename)
    {
        $filePath = array(
            'base_path' => base_path($basename),
            'storage_path' => storage_path($basename),
            'public_path' => public_path($basename),
        );
        return substr($filePath[$path],0,strlen($filePath[$path])-1);
    }
}

if (!function_exists('open_file'))
{
    /**
     * 文件读取
     * @param $filepath
     * @return string
     */
    function open_file($filepath)
    {
        $fileObj = new SplFileObject(str_replace(public_path(),'',$filepath),'r');
        static $fileInfo = '';
        while ($fileObj->valid()){
            $fileInfo.= $fileObj->fgets();
        }
        $fileObj = null;
        $file = null;
        return $fileInfo;
    }
}
if (!function_exists('write_file'))
{
    /**
     * 文件写入
     * @param $filepath
     * @param $content
     * @return int
     */
    function write_file($filepath,$content)
    {
        $file = new SplFileObject($filepath,'w');
        $written = $file->fwrite($content);
        return $written;
    }
}
if (!function_exists('file_rename')){
    /**
     * todo：文件重命名
     * @param $oldFile
     * @param $newFile
     * @return bool
     */
    function file_rename($oldFile,$newFile)
    {
        if ((is_file($oldFile) || is_dir($oldFile)) && !file_exists($newFile)){
            return rename($oldFile,$newFile);
        }
        return false;
    }
}
if (!function_exists('save_file')){
    /**
     * todo：保存文件
     * @param $filepath
     * @return bool
     */
    function save_file($filepath)
    {
        if (file_exists($filepath)){
            return false;
        }
        $file = mb_substr($filepath,strripos($filepath,'/')+1);
        if (strstr($file,'.') === false){
            mkdir($filepath);
            return true;
        }
        $fileObj = new SplFileObject($filepath,'a');
        $written = $fileObj->fwrite(basename($filepath));
        return $written;
    }
}

if (!function_exists('file_chmod')){
    /**
     * todo：获取权限
     * @param $filepath
     * @return bool|string
     */
    function file_chmod($filepath){
        return substr(base_convert(@fileperms($filepath),10,8),-3);
    }
}

if (!function_exists('gzip'))
{
    /**
     * 文件压缩
     * @param array $docLists 文件列表
     * @param string $zipProductPath 文件路径
     * @param string $filename 文件名
     * @return bool
     */
    function gzip($docLists,$zipProductPath,$filename)
    {
        $zipObj = new ZipArchive();
        $result = $zipObj->open($zipProductPath.$filename,ZipArchive::CREATE);
        if(!$result){  //创建zip文件
            return false;
        }
        foreach ($docLists as $item){
            if (is_dir($item)){
                add_file_to_zip($item,$zipObj);
            }else{
                $zipObj->addFile($item);
            }
        }
        $zipObj->close();
        return file_exists($zipProductPath.$filename);
    }
}
if (!function_exists('add_file_to_zip'))
{
    /**
     * @param $path
     * @param ZipArchive $zip
     */
    function add_file_to_zip($path,$zip){
        //打开当前文件夹由$path指定。
        $handler=opendir($path);
        while(($filename=readdir($handler))!==false){
            //文件夹文件名字为'.'和‘..’，不要对他们进行操作
            if(!in_array($filename,['.','..'])){
                //如果读取的某个对象是文件夹，则递归
                if(is_dir($path.DIRECTORY_SEPARATOR.$filename)){
                    add_file_to_zip($path.DIRECTORY_SEPARATOR.$filename, $zip);
                }else{
                    //将文件加入zip对象
                    $zip->addFile($path.DIRECTORY_SEPARATOR.$filename);
                }
            }
        }
        @closedir($path);
    }
}

if (!function_exists('unzip'))
{
    /**
     * 文件解压
     * @param string $path 文件路径
     * @param string $resource 解压包文件名称
     * @return bool
     */
    function unzip($path,$resource='')
    {
        if(!file_exists($path)){
            return false;
        }
        $zip=new ZipArchive();
        $arr = explode('.',basename($path));
        if(!in_array($arr[1],['zip','ZIP'])){
            return false;
        }
        $filePath = str_replace(basename($path),'',$path).(empty($resource) ? $arr [0] : $resource);
        if(!is_dir($filePath)){
            mkdir($filePath,0777,true);
        }
        if($zip->open($path)){     //打开zip文件返回bool值
            $zip->extractTo($filePath);   //将完整的归档或给定文件提取到指定的目标
        }
        $zip->close();    //关闭资源
        return true;
    }
}
if(!function_exists('remove_files'))
{
    /**
     * 删除文件
     * @param $path
     * @return bool
     */
    function remove_files($path)
    {
        if (is_file($path)){
            try{
                return unlink($path);
            }catch (Exception $exception){
                return false;
            }
        }
        //先删除目录下的文件：
        $dh=opendir($path);
        while ($file=readdir($dh)) {
            if(!in_array($file,['.','..'])) {
                $fullPath=$path.DIRECTORY_SEPARATOR.$file;
                if(!is_dir($fullPath)) {
                    unlink($fullPath);
                } else {
                    remove_files($fullPath);
                }
            }
        }
        @closedir($dh);
        //删除当前文件夹：
        if(rmdir($path)) {
            return true;
        } else {
            return false;
        }
    }
}
if(!function_exists('empty_dir'))
{
    /**
     * 判断目录是否为空
     * @param $path
     * @return bool
     */
    function empty_dir($path)
    {
        //array_diff() 函数返回两个数组的差集数组。该数组包括了所有在被比较的数组中，但是不在任何其他参数数组中的键值。
        //在返回的数组中，键名保持不变。以第一个数组座位参考对象
        //scandir() 函数返回指定目录中的文件和目录的数组。
        $a=array_diff(scandir($path),array('..','.'));
        //说明该目录不至是存在 .. 和 . 还有其他的文件;
        if(!empty($a)){
            return false;
        }
        return true;
    }
}
if(!function_exists('is_mobile'))
{
    /**
     * 检测是否是手机访问
     */
    function is_mobile(){
        $useragent=isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $useragent_comments_block=preg_match('|\(.*?\)|',$useragent,$matches)>0?$matches[0]:'';
        function _is_mobile($substr,$text){
            foreach($substr as $str)
                if(false!==strpos($text,$str)){
                    return true;
                }
            return false;
        }
        $mobile_os_list=array('Google Wireless Transcoder','Windows CE','WindowsCE','Symbian','Android','armv6l','armv5','Mobile','CentOS','mowser','AvantGo','Opera Mobi','J2ME/MIDP','Smartphone','Go.Web','Palm','iPAQ');
        $mobile_token_list=array('Profile/MIDP','Configuration/CLDC-','160×160','176×220','240×240','240×320','320×240','UP.Browser','UP.Link','SymbianOS','PalmOS','PocketPC','SonyEricsson','Nokia','BlackBerry','Vodafone','BenQ','Novarra-Vision','Iris','NetFront','HTC_','Xda_','SAMSUNG-SGH','Wapaka','DoCoMo','iPhone','iPod');
        $found_mobile=_is_mobile($mobile_os_list,$useragent_comments_block) ||
            _is_mobile($mobile_token_list,$useragent);
        if ($found_mobile){
            return true;
        }else{
            return false;
        }
    }
}
if(!function_exists('authcode'))
{
    /**
     * @param $string
     * @param string $operation  DECODE 解密   ENCODE 加密
     * @param string $key
     * @param int $expiry
     * @return bool|string
     */
    function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
        $ckey_length = 4;
        // 密匙
        $key = md5($key ? $key : config('key'));
        // 密匙a会参与加解密
        $keya = md5(substr($key, 0, 16));
        // 密匙b会用来做数据完整性验证
        $keyb = md5(substr($key, 16, 16));
        // 密匙c用于变化生成的密文
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length):
            substr(md5(microtime()), -$ckey_length)) : '';
        // 参与运算的密匙
        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);
        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
        // 解密时会通过这个密匙验证数据完整性
        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
            sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        // 产生密匙簿
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        // 核心加解密部分
        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            // 从密匙簿得出密匙进行异或，再转成字符
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if($operation == 'DECODE') {
            // 验证数据有效性，请看未加密明文的格式
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
                substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
            return $keyc.str_replace('=', '', base64_encode($result));
        }
    }
}
if(!function_exists('get_location_by_ip'))
{
    /**
     * 依据ip获地址信息 (高德地图)
     * @param string $ip
     * @return int|mixed
     */
    function get_location_by_ip($ip='')
    {
        $data['key'] = config("app.amap_key");
        if(!empty($ip)){
            $data['ip'] = $ip;
        }
        $result = http_query('http://restapi.amap.com/v3/ip', $data);
        $result = json_decode($result, true);
        return $result;
    }
}
if (!function_exists('act_log'))
{
    /**
     * 添加日志
     * @param $info
     * @return bool
     */
    function act_log($info)
    {
        $data = array(
            'username' =>$info['username'],
            'url' =>$info['href'],
            'ip_address' =>request()->getClientIp(),
            'created_at' =>time(),
            'log' =>$info,
        );
        $data['log'] =str_replace('\\','', json_encode($data,JSON_UNESCAPED_UNICODE));
        return Log::getInstance()->addResult($data);
    }
}

if (!function_exists('get_xing_lists'))
{
    //获取姓氏
    function get_xing_lists(){
        $arrXing=array('赵','钱','孙','李','周','吴','郑','王','冯','陈','褚','卫','蒋','沈','韩','杨','朱','秦','尤','许','何','吕','施','张','孔','曹','严','华','金','魏','陶','姜','戚','谢','邹',
            '喻','柏','水','窦','章','云','苏','潘','葛','奚','范','彭','郎','鲁','韦','昌','马','苗','凤','花','方','任','袁','柳','鲍','史','唐','费','薛','雷','贺','倪','汤','滕','殷','罗',
            '毕','郝','安','常','傅','卞','齐','元','顾','孟','平','黄','穆','萧','尹','姚','邵','湛','汪','祁','毛','狄','米','伏','成','戴','谈','宋','茅','庞','熊','纪','舒','屈','项','祝',
            '董','梁','杜','阮','蓝','闵','季','贾','路','娄','江','童','颜','郭','梅','盛','林','钟','徐','邱','骆','高','夏','蔡','田','樊','胡','凌','霍','虞','万','支','柯','管','卢','莫',
            '柯','房','裘','缪','解','应','宗','丁','宣','邓','单','杭','洪','包','诸','左','石','崔','吉','龚','程','嵇','邢','裴','陆','荣','翁','荀','于','惠','甄','曲','封','储','仲','伊',
            '宁','仇','甘','武','符','刘','景','詹','龙','叶','幸','司','黎','溥','印','怀','蒲','邰','从','索','赖','卓','屠','池','乔','胥','闻','莘','党','翟','谭','贡','劳','逄','姬','申',
            '扶','堵','冉','宰','雍','桑','寿','通','燕','浦','尚','农','温','别','庄','晏','柴','瞿','阎','连','习','容','向','古','易','廖','庾','终','步','都','耿','满','弘','匡','国','文',
            '寇','广','禄','阙','东','欧','利','师','巩','聂','关','荆','司马','上官','欧阳','夏侯','诸葛','闻人','东方','赫连','皇甫','尉迟','公羊','澹台','公冶','宗政','濮阳','淳于','单于','太叔',
            '申屠','公孙','仲孙','轩辕','令狐','徐离','宇文','长孙','慕容','司徒','司空');
        return $arrXing;
    }
}
if (!function_exists('get_ming_lists'))
{
    //获取名字
    function get_ming_lists(){
        $arrMing=array('伟','刚','勇','毅','俊','峰','强','军','平','保','东','文','辉','力','明','永','健','世','广','志','义','兴','良','海','山','仁','波','宁','贵','福','生','龙','元','全'
        ,'国','胜','学','祥','才','发','武','新','利','清','飞','彬','富','顺','信','子','杰','涛','昌','成','康','星','光','天','达','安','岩','中','茂','进','林','有','坚','和','彪','博','诚'
        ,'先','敬','震','振','壮','会','思','群','豪','心','邦','承','乐','绍','功','松','善','厚','庆','磊','民','友','裕','河','哲','江','超','浩','亮','政','谦','亨','奇','固','之','轮','翰'
        ,'朗','伯','宏','言','若','鸣','朋','斌','梁','栋','维','启','克','伦','翔','旭','鹏','泽','晨','辰','士','以','建','家','致','树','炎','德','行','时','泰','盛','雄','琛','钧','冠','策'
        ,'腾','楠','榕','风','航','弘','秀','娟','英','华','慧','巧','美','娜','静','淑','惠','珠','翠','雅','芝','玉','萍','红','娥','玲','芬','芳','燕','彩','春','菊','兰','凤','洁','梅','琳'
        ,'素','云','莲','真','环','雪','荣','爱','妹','霞','香','月','莺','媛','艳','瑞','凡','佳','嘉','琼','勤','珍','贞','莉','桂','娣','叶','璧','璐','娅','琦','晶','妍','茜','秋','珊','莎'
        ,'锦','黛','青','倩','婷','姣','婉','娴','瑾','颖','露','瑶','怡','婵','雁','蓓','纨','仪','荷','丹','蓉','眉','君','琴','蕊','薇','菁','梦','岚','苑','婕','馨','瑗','琰','韵','融','园'
        ,'艺','咏','卿','聪','澜','纯','毓','悦','昭','冰','爽','琬','茗','羽','希','欣','飘','育','滢','馥','筠','柔','竹','霭','凝','晓','欢','霄','枫','芸','菲','寒','伊','亚','宜','可','姬'
        ,'舒','影','荔','枝','丽','阳','妮','宝','贝','初','程','梵','罡','恒','鸿','桦','骅','剑','娇','纪','宽','苛','灵','玛','媚','琪','晴','容','睿','烁','堂','唯','威','韦','雯','苇','萱'
        ,'阅','彦','宇','雨','洋','忠','宗','曼','紫','逸','贤','蝶','菡','绿','蓝','儿','翠','烟');
        return $arrMing;
    }
}
if (!function_exists('get_distance'))
{
    /**
     * 根据两点间的经纬度计算距离
     * @param $lat1
     * @param $lng1
     * @param $lat2
     * @param $lng2
     * @return float
     */
    function get_distance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6378138; //近似地球半径米
        // 转换为弧度
        $lat1 = ($lat1 * pi()) / 180;
        $lng1 = ($lng1 * pi()) / 180;
        $lat2 = ($lat2 * pi()) / 180;
        $lng2 = ($lng2 * pi()) / 180;
        // 使用半正矢公式  用尺规来计算
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        return round($calculatedDistance);
    }
}
if (!function_exists('base64_image_content'))
{
    /**
     * [将Base64图片转换为本地图片并保存]
     * @param $base64_image_content
     * @param $path
     * @param $id
     * @return array|bool
     */
    function base64_image_content($base64_image_content,$path,$id)
    {
        $mainSiteUrl = 'https://www.fanglonger.com';  //host 自定义
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
            $new_file = $path."/";
            if(!file_exists($new_file)){
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($new_file, 0700);
            }
            $new_file = $new_file.$id.".png";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))){
                $qrcode = $mainSiteUrl.'/Uploads/qrcode/'.date("Ymd").'/'.$id.".png";
                return $qrcode;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
if (!function_exists('grab_image'))
{
    /**
     * 下载图片保存指定位置
     * @param $url
     * @param $dir
     * @param string $filename
     * @return bool|string
     */
    function GrabImage($url, $dir, $filename='')
    {
        if(empty($url)){
            return false;
        }
        $ext = strrchr($url, '.');
        if($ext != '.gif' && $ext != ".jpg" && $ext != ".bmp" && $ext!='png'){
            echo "格式不支持！";
            return false;
        }
        $url =
        $dir = realpath($dir);
        //目录+文件
        $filename = (empty($filename) ? '/'.time().uniqid().''.$ext : '/'.$filename);
        $filename = $dir . $filename;
        //开始捕捉
        ob_start();
        readfile($url);
        $img = ob_get_contents();
        ob_end_clean();
        $fp2 = fopen($filename , "a");
        fwrite($fp2, $img);
        fclose($fp2);
        return $filename;
    }
}
if (!function_exists('cut_file'))
{

    /**
     * todo：文件的分割
     * @param string $filename 文件名
     * @param string $block 文件分割大小
     * @return bool
     */
    function cut_file($filename,$block){
        //判断是不是一个文件
        if(!file_exists($filename)){
            return false;
        }
        $inputFileObj = new SplFileObject($filename);
        if (!in_array(substr(sprintf('%o', $inputFileObj->getPerms()), -4),[0777,777])){
            chmod($filename,0777);
        }
        while ($content=$inputFileObj->fread($block)){
            $cutFileName = $inputFileObj->getPath().'/'.explode('.',$inputFileObj->getFilename())[0].'_'.uniqid().'.'.$inputFileObj->getExtension();
            $cutFileObj = new SplFileObject($cutFileName,'w+');
            $cutFileObj->fwrite( $content);
            $cutFileObj = null;
        }
        $inputFileObj = null;
        remove_files($filename);
        return true;
    }
}
if (!function_exists('merger_file'))
{
    /**
     * 合并文件
     * @param $targetFile
     * @param $filePath
     */
    function merger_file($targetFile,$filePath){
        $targetFileObj = new SplFileObject($targetFile,'w+');
        $content='';
        foreach ($filePath as $file){
            $fileObj = new SplFileObject($file,'r');
            while ($fileObj->valid()){
                $content.=$fileObj->fgets();
            }
            $fileObj = null;
        }
        $targetFileObj->fwrite($content);
        $targetFileObj = null;
    }
}
