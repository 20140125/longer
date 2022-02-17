<?php

namespace App\Http\Controllers\Service\MiniProgram;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\Code;
use App\Models\Api\v1\Oauth;
use App\Models\Api\v1\SooGif;
use App\Models\Api\v1\SooGifType;
use App\Models\Api\v1\SystemConfig;

class BaseService extends Controller
{
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @var static $instance
     */
    private static $instance;

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
     * @var mixed
     */
    protected $appid;
    /**
     * @var mixed
     */
    protected $appSecret;
    /**
     * @var Oauth $oauthModel
     */
    protected $oauthModel;
    /**
     * @var SystemConfig $systemConfigModel
     */
    protected $systemConfigModel;
    /**
     * @var SooGif $sooGifModel
     */
    protected $sooGifModel;
    /**
     * @var SooGifType $sooGifTypeModel
     */
    protected $sooGifTypeModel;
    /**
     * @var $systemConfig
     */
    protected $systemConfig;
    /**
     * @var $hotKeyWords
     */
    protected $configuration;
    /**
     * @var array $return
     */
    protected $return;

    public function __construct()
    {
        $this->appid = config('app.mini_program_appid');
        $this->appSecret = config('app.mini_program_secret');
        $this->oauthModel = Oauth::getInstance();
        $this->systemConfigModel = SystemConfig::getInstance();
        $this->sooGifModel = SooGif::getInstance();
        $this->sooGifTypeModel = SooGifType::getInstance();
        /* todo:信息输出 */
        $this->return = array('code' => Code::SUCCESS, 'message' => 'successfully', 'lists' => []);
        /* todo：获取小程序配置 */
        $this->getSystemConfiguration();
    }

    /**
     * todo:获取系统配置
     * @param string $name
     * @return mixed
     */
    public function getSystemConfiguration(string $name = 'ImageBed')
    {
        $this->systemConfig = $this->systemConfigModel->getOne(['name' => $name], ['children'])->children;
        return $this->systemConfig;
    }

    /**
     * todo:获取配置
     * @param $key
     * @return mixed|string
     */
    public function getSystemConfig($key)
    {
        $value = '';
        $systemConfig = json_decode($this->systemConfig, true);
        foreach ($systemConfig as $item) {
            if ($item['name'] === $key) {
                $value = $item['value'];
            }
        }
        return $value;
    }

    /**
     * todo:获取配置
     * @param string $keyWords
     * @return string[]
     */
    public function getConfiguration(string $keyWords = 'hotKeyWord')
    {
        $this->configuration = $this->getSystemConfig($keyWords);
        $this->return['lists'] = explode(',', $this->configuration);
        return $this->return;
    }
}
