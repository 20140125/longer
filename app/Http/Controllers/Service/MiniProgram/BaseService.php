<?php

namespace App\Http\Controllers\Service\MiniProgram;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\Code;
use App\Models\Api\v1\Oauth;
use App\Models\Api\v1\SooGif;
use App\Models\Api\v1\SooGifType;
use App\Models\Api\v1\SystemConfig;
use Carbon\Carbon;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Cache;

class BaseService extends Controller
{
    /**
     * @var Repository|Application|mixed
     */
    protected $appid;
    /**
     * @var Repository|Application|mixed
     */
    protected $appsecret;
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
    protected $hotKeyWords;
    /**
     * @var array $return
     */
    protected $return;

    public function __construct()
    {
        $this->appid = config('app.mini_program_appid');
        $this->appsecret = config('app.mini_program_secret');
        $this->oauthModel = Oauth::getInstance();
        $this->systemConfigModel = SystemConfig::getInstance();
        $this->sooGifModel = SooGif::getInstance();
        $this->sooGifTypeModel = SooGifType::getInstance();
        /* todo:信息输出 */
        $this->return = array('code' => Code::SUCCESS, 'message' => 'successfully', 'lists' => []);
        /* todo：获取小程序配置 */
        $this->getImageConfiguration();
    }

    /**
     * @return mixed
     */
    public function getImageConfiguration()
    {
        $this->systemConfig = Cache::get('mini_program_image_configuration');
        if (!$this->systemConfig ) {
            $this->systemConfig  = $this->systemConfigModel->getOne(['name' => 'ImageBed'], ['children'])->children;
            Cache::put('mini_program_image_configuration', $this->systemConfig, Carbon::now()->addHours(2));
        }
        return $this->systemConfig ;
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
     * todo:获取关键词
     * @return string[]
     */
    public function getHotKeyWords()
    {
        $this->hotKeyWords = $this->getSystemConfig('hotKeyWord');
        $this->return['lists'] = explode(',', $this->hotKeyWords);
        return $this->return;
    }
}
