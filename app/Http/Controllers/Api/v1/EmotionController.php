<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Emotion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Class EmotionController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class EmotionController extends BaseController
{
    /**
     * @var Emotion $emotionModel
     */
    protected $emotionModel;

    /**
     * EmotionController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->emotionModel = Emotion::getInstance();
    }

    /**
     * TODO:获取表情
     * @param integer type 分类
     * @param integer page
     * @param integer limit
     * @return JsonResponse
     */
    public function index()
    {
        $this->validatePost(['type'=>'required|integer','page'=>'required|integer|gt:0','limit'=>'required|integer|lt:56']);
        $result = Cache::get($this->post['type'].$this->post['page']);
        if (!Cache::has($this->post['type'].$this->post['page'])) {
            $result = $this->emotionModel->getListByType($this->post['type'],$this->post['page']??1,$this->post['limit']??55);
            Cache::forever($this->post['type'].$this->post['page'],$result);
        }
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }
}
