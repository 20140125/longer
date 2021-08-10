<?php

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\Controller;
use Curl\Curl;
use Illuminate\Config\Repository;

/**
 * Class AMap
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Utils
 */
class AMap extends Controller
{
    /**
     * @var AMap $instance
     */
    protected static $instance;
    /**
     * @var Repository|mixed
     */
    protected $a_map_key;
    /**
     * @var string $url
     */
    protected $url;
    /**
     * @var array $data
     */
    protected $data = array();
    /**
     * @var Curl $Curl
     */
    protected $Curl;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }
    /**
     * AMap constructor.
     */
    public function __construct()
    {
        $this->a_map_key = config('app.a_map_key');
        $this->url = 'https://restapi.amap.com/v3/';
        $this->data = array('key' =>$this->a_map_key, 'output' =>'json');
        $this->Curl = new  Curl();
    }

    /**
     * TODO:获取天气状况
     * @param $ad_code
     * @param string $extensions
     * @return mixed
     */
    public function getWeather($ad_code, string $extensions = 'base')
    {
        try {
            $weatherUrl = 'weather/weatherInfo';
            $this->data['city'] = $ad_code;
            $this->data['extensions'] = $extensions;
            return $this->Curl->get($this->url.$weatherUrl, $this->data);
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }

    /**
     * TODO:根据IP地位
     * @param string $ipAddress
     * @return int|mixed|null
     */
    public function getAddress(string $ipAddress = '127.0.0.1')
    {
        try {
            $ipUrl = 'ip';
            $this->data['ip'] = $ipAddress;
            return $this->Curl->get($this->url.$ipUrl, $this->data);
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }

    /**
     * TODO:地理/逆地理编码
     * @param $city
     * @param string $address
     * @return int|mixed|null
     */
    public function geoCode($city, string $address = '')
    {
        try {
            $geoUrl = 'geocode/geo';
            $this->data['city'] = $city;
            $this->data['address'] = $address;
            return $this->Curl->get($this->url.$geoUrl, $this->data);
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }

    /**
     * TODO:路径规划
     * @param string $origin  出发点 规则： lon，lat（经度，纬度）， “,”分割，如117.500244, 40.417801     经纬度小数点不超过6位
     * @param  string $destination  目的地  规则： lon，lat（经度，纬度）， “,”分割，如117.500244, 40.417801     经纬度小数点不超过6位
     * @return int|mixed|null
     */
    public function direction(string $origin, string $destination)
    {
        try {
            $directionUrl = 'direction/walking';
            $this->data['origin'] = $origin;
            $this->data['destination'] = $destination;
            return $this->Curl->get($this->url.$directionUrl, $this->data);
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }

    /**
     * TODO:可在此location附近优先返回搜索关键词信息
     * @param string $keywords
     * @param string $location 建议使用location参数，可在此location附近优先返回搜索关键词信息
     * @param string $city 可选值：citycode、adcode
     * @return int|mixed|null
     */
    public function inputTips(string $keywords, string $location = '', string $city = '')
    {
        try {
            $assistantUrl = 'assistant/inputtips';
            $this->data['keywords'] = $keywords;
            $this->data['location'] = $location;
            $this->data['city'] = $city;
            return $this->Curl->get($this->url.$assistantUrl, $this->data);
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
