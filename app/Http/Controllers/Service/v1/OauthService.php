<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class OauthService extends BaseService
{
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
     * todo:获取授权用户
     * @param $where
     * @param string[] $columns
     * @return Model|Builder|object|null
     */
    public function getOauth($where, $columns = ['*'])
    {
        return $this->oauthModel->getOne($where, $columns);
    }
}
