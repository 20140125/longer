<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\Code;

/**
 * Class ApiService
 * @package App\Http\Controllers\Service\v1
 */
class ApiService extends BaseService
{
    /**
     * @var static $instance
     */
    private static $instance;

    protected $json_str = ['request', 'response', 'response_string'];
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
     * todo：获取编辑器格式接口详情
     * @param $form
     * @return array
     */
    public function getApiList($form)
    {
        $this->return['lists'] = $this->apiListsModel->getOne(['api_id' => $form['id']]);
        if(!$this->return['lists']) {
            $this->return['lists'] = (object)array();
            return $this->return;
        }
        foreach ($this->json_str as $item) {
            $this->return['lists']->$item = json_decode($this->return['lists']->$item, JSON_UNESCAPED_UNICODE);
        }
        $apiLog = $this->apiLogModel->getLists(['api_id' => $this->return['lists']->id, 'source' => 2], ['order' => 'id', 'direction' => 'desc'], ['username', 'updated_at', 'json', 'desc', 'source']);
        foreach ($apiLog as &$item) {
            $item->json = json_decode($item->json, true);
            $item->updated_at = date('Y-m-d H:i:s', $item->updated_at);
        }
        $this->return['lists']->apiLog = $apiLog;
        $this->return['lists']->source = 'json';
        return $this->return;
    }

    /**
     * todo：获取markdown格式接口详情
     * @param $form
     * @return array
     */
    public function getMarkDownList($form)
    {
        $this->return['lists'] = $this->apiDocModel->getOne(['api_id' => $form['id']]);
        if(!$this->return['lists']) {
            $this->return['lists'] = (object)array();
            return $this->return;
        }
        $apiLog = $this->apiLogModel->getLists(['api_id' => $this->return['lists']->id, 'source' => 1], ['order' => 'id', 'direction' => 'desc'], ['username', 'updated_at', 'json', 'desc', 'source']);
        foreach ($apiLog as &$item) {
            $item->updated_at = date('Y-m-d H:i:s', $item->updated_at);
        }
        $this->return['lists']->apiLog = $apiLog;
        $this->return['lists']->source = 'markdown';
        return $this->return;
    }

    /**
     * todo:保存API接口详情
     * @param $form
     * @param $user
     * @return array
     */
    public function saveApiLists($form, $user)
    {
        if (!empty($form['source'])) unset($form['source']);
        if (count($form['apiLog']) >= 0) unset($form['apiLog']);
        foreach ($this->json_str as $item) {
            $form[$item] = json_encode($form[$item], JSON_UNESCAPED_UNICODE);
        }
        $result = $this->apiListsModel->saveOne($form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'Error saving json interface details';
            return $this->return;
        }
        $form['source'] = 2;
        $this->saveApiLog($form, $user);
        $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * todo:更新API接口详情
     * @param $form
     * @param $user
     * @return array
     */
    public function updateApiLists($form, $user)
    {
        if (!empty($form['source'])) unset($form['source']);
        if (count($form['apiLog']) >= 0) unset($form['apiLog']);
        foreach ($this->json_str as $item) {
            $form[$item] = json_encode($form[$item], JSON_UNESCAPED_UNICODE);
        }
        $result = $this->apiListsModel->updateOne(['id' => $form['id']],$form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'Error update json interface details';
            return $this->return;
        }
        $form['source'] = 2;
        $this->saveApiLog($form, $user);
        $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * todo:保存markdown
     * @param $form
     * @param $user
     * @return array
     */
    public function saveMarkDown($form, $user)
    {
        if (!empty($form['source'])) unset($form['source']);
        if (count($form['apiLog']) >= 0) unset($form['apiLog']);
        $result = $this->apiDocModel->saveOne($form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'Error saving markdown interface details';
            return $this->return;
        }
        $form['source'] = 1;
        $this->saveApiLog($form, $user);
        $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * todo:更新markdown
     * @param $form
     * @param $user
     * @return array
     */
    public function updateMarkDown($form, $user)
    {
        if (!empty($form['source'])) unset($form['source']);
        if (count($form['apiLog']) >= 0) unset($form['apiLog']);
        $result = $this->apiDocModel->updateOne(['id' => $form['id']],$form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'Error update markdown interface details';
            return $this->return;
        }
        $form['source'] = 1;
        $this->saveApiLog($form, $user);
        $this->return['lists'] = $form;
        return $this->return;
    }
    /**
     * todo:保存操作日志
     * @param $form
     * @param $user
     */
    protected function saveApiLog($form, $user)
    {
        $this->apiLogModel->saveOne([
            'username' => $user->username,
            'api_id' => $form['api_id'],
            'updated_at' => time(),
            'source' => $form['source'],
            'desc' => '编辑'.($form['desc'] ?? $this->apiCategoryModel->getOne(['id' => $form['api_id']], ['name'])->name),
            'json' => $form['source'] === 2 ? json_encode($form, JSON_UNESCAPED_UNICODE) : $form['markdown']
        ]);
    }
}
