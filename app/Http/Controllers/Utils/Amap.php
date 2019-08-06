<?php

namespace App\Http\Controllers\Utils;

use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Amap
{
    protected static $instance;
    /**
     * @var Repository|mixed
     */
    protected $amapkey;
    /**
     * @var string $url
     */
    protected $url;

    protected $data = array();

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new static();
        }
        return self::$instance;
    }
    public function __construct()
    {
        $this->amapkey = config('app.amap_key');
        $this->url = 'https://restapi.amap.com/v3/';
        $this->data = array(
            'key' =>$this->amapkey,
            'output' =>'json'
        );
    }

    /**
     * 获取天气状况
     * @param $adcode
     * @return int|mixed|null
     */
    public function getWeather($adcode)
    {
        $weatherUrl = 'weather/weatherInfo';
        $this->data['city'] = $adcode;
        $this->data['extensions'] = 'base';
        $weatherInfo = http_query($this->url.$weatherUrl,$this->data);
        return $weatherInfo;
    }

    /**
     * 根据IP地位
     * @param $ipAddress
     * @return int|mixed|null
     */
    public function getAddress($ipAddress)
    {
        $ipUrl = 'ip';
        $this->data['ip'] = $ipAddress;
        $ipInfo = http_query($this->url.$ipUrl,$this->data);
        return $ipInfo;
    }

    /**
     * 地理/逆地理编码
     * @param $address
     * @param $city
     * @return int|mixed|null
     */
    public function geoCode($city,$address)
    {
        $geoUrl = 'geocode/geo';
        $this->data['city'] = $city;
        $this->data['address'] = $address;
        $geoInfo = http_query($this->url.$geoUrl,$this->data);
        return $geoInfo;
    }

    /**
     * 路径规划
     * @param string $origin  出发点 规则： lon，lat（经度，纬度）， “,”分割，如117.500244, 40.417801     经纬度小数点不超过6位
     * @param  string $destination  目的地  规则： lon，lat（经度，纬度）， “,”分割，如117.500244, 40.417801     经纬度小数点不超过6位
     * @return int|mixed|null
     */
    public function direction($origin,$destination)
    {
        $directionUrl = 'direction/walking';
        $this->data['origin'] = $origin;
        $this->data['destination'] = $destination;
        $geoInfo = http_query($this->url.$directionUrl,$this->data);
        return $geoInfo;
    }

    /**
     * @param $keywords
     * @param string $location 建议使用location参数，可在此location附近优先返回搜索关键词信息
     * @param string  $city 可选值：citycode、adcode
     * @return int|mixed|null
     */
    public function inputTips($keywords,$location,$city)
    {
        $assistantUrl = 'assistant/inputtips';
        $this->data['keywords'] = $keywords;
        $this->data['location'] = $location;
        $this->data['city'] = $city;
        $assistantInfo = http_query($this->url.$assistantUrl,$this->data);
        return $assistantInfo;
    }
}
