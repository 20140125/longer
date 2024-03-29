<?php

namespace App\Http\Controllers\Service\MiniProgram;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\Code;
use App\Models\Api\v1\Oauth;
use App\Models\Api\v1\SooGif;
use App\Models\Api\v1\SooGifType;
use App\Models\Api\v1\SystemConfig;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;

class BaseService extends Controller
{
    private function __clone()
    {
        // Implement __clone() method.
    }

    /**
     * @var Repository|Application|mixed
     */
    protected $appid;
    /**
     * @var Repository|Application|mixed
     */
    protected $appSecret;
    /**
     * @var Oauth $oauthModel
     */
    protected Oauth $oauthModel;
    /**
     * @var SystemConfig $systemConfigModel
     */
    protected SystemConfig $systemConfigModel;
    /**
     * @var SooGif $sooGifModel
     */
    protected SooGif $sooGifModel;
    /**
     * @var SooGifType $sooGifTypeModel
     */
    protected SooGifType $sooGifTypeModel;
    /**
     * @var string $systemConfig
     */
    protected string $systemConfig;
    /**
     * @var string $configuration
     */
    protected string $configuration;
    /**
     * @var array $return
     */
    protected array $return;

    public function __construct()
    {
        $this->appid = config('app.mini_program_appid');
        $this->appSecret = config('app.mini_program_secret');
        $this->oauthModel = Oauth::getInstance();
        $this->systemConfigModel = SystemConfig::getInstance();
        $this->sooGifModel = SooGif::getInstance();
        $this->sooGifTypeModel = SooGifType::getInstance();
        /* 信息输出 */
        $this->return = array('code' => Code::SUCCESS, 'message' => 'successfully', 'lists' => []);
        /* 获取小程序配置 */
        $this->getSystemConfiguration();
    }

    /**
     * 获取系统配置
     * @param string $name
     * @return mixed
     */
    public function getSystemConfiguration(string $name = 'ImageBed')
    {
        $this->systemConfig = $this->systemConfigModel->getOne(['name' => $name], ['children'])->children;
        return $this->systemConfig;
    }

    /**
     * 获取配置
     * @param $key
     * @return string
     */
    public function getSystemConfig($key): string
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
     * 获取配置
     * @param string $keyWords
     * @return string[]
     */
    public function getConfiguration(string $keyWords = 'hotKeyWord'): array
    {
        $this->configuration = $this->getSystemConfig($keyWords);
        $this->return['lists'] = explode(',', $this->configuration);
        return $this->return;
    }
}
