<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\Code;
use App\Http\Middleware\Base;
use Illuminate\Http\Request;

class ApiService extends BaseService
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
     * todo:获取分类列表
     * @param array $where
     * @param array|string[] $order
     * @param array|string[] $columns
     * @return array
     */
    public function getCategoryLists(array $where = [], array $order = ['order' => 'id', 'direction' => 'desc'], array $columns = ['*'])
    {
        $this->return['lists'] = $this->apiCategoryModel->getLists($where, $order, $columns);
        return $this->return;
    }

    /**
     * todo：获取编辑器格式接口详情
     * @param $form
     * @return array
     */
    public function getApiList($form)
    {
        $this->return['lists'] = $this->apiListsModel->getOne(['type' => $form['id']]);
        return $this->return;
    }

    /**
     * todo：获取markdown格式接口详情
     * @param $form
     * @return array
     */
    public function getMarkDownList($form)
    {
        $this->return['lists'] = $this->apiDocModel->getOne(['type' => $form['id']]);
        return $this->return;
    }

    /**
     * todo:保存API接口详情
     * @param $form
     * @return array
     */
    public function saveApiLists($form)
    {
        if (!empty($form['source'])) unset($form['source']);
        $json_str = ['request', 'response', 'response_string'];
        foreach ($json_str as $item) {
            $form[$item] = json_encode($form[$item], JSON_UNESCAPED_UNICODE);
        }
        $result = $this->apiListsModel->saveOne($form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'Failed saving text interface details';
            return $this->return;
        }
        $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * todo:更新API接口详情
     * @param $form
     * @return array
     */
    public function updateApiLists($form)
    {
        if (!empty($form['source'])) unset($form['source']);
        $json_str = ['request', 'response', 'response_string'];
        foreach ($json_str as $item) {
            $form[$item] = json_encode($form[$item], JSON_UNESCAPED_UNICODE);
        }
        $result = $this->apiListsModel->updateOne(['id' => $form['id']],$form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'Error update text interface details';
            return $this->return;
        }
        $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * todo:保存markdown
     * @param $form
     * @return array
     */
    public function saveMarkDown($form)
    {
        if (!empty($form['source'])) unset($form['source']);
        $result = $this->apiDocModel->saveOne($form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'Failed saving markdown interface details';
            return $this->return;
        }
        $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * todo:更新markdown
     * @param $form
     * @return array
     */
    public function updateMarkDown($form)
    {
        if (!empty($form['source'])) unset($form['source']);
        $result = $this->apiDocModel->updateOne(['id' => $form['id']],$form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'Error update markdown interface details';
            return $this->return;
        }
        $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * todo:数据添加
     * @param $form
     * @return array
     */
    public function saveCategory($form)
    {
        $id = $this->apiCategoryModel->saveOne($form);
        if (!$id) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'save category failed';
            return $this->return;
        }
        $parent_result = $this->apiCategoryModel->getOne(['id' => $form['pid']], ['path']);
        $form['path'] = $id;
        $form['level'] = 0;
        if (!empty($parent_result)) {
            $form['path'] = $parent_result->path.'-'.$id;
            $form['level'] = substr_count($form['path'], '-');
        }
        $result = $this->apiCategoryModel->updateOne(['id' => $id], $form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'save category failed';
            return $this->return;
        }
        $this->return['lists'] = $form;
        return $this->return;
    }
    /**
     * todo:数据更新
     * @param $form
     * @return array
     */
    public function updateCategory($form)
    {
        $parent_result = $this->apiCategoryModel->getOne(['id' => $form['pid']], ['path']);
        $form['path'] = !empty($parent_result->path) ? $parent_result->path.'-'.$form['id'] : $form['id'];
        $form['level'] = substr_count($form['path'], '-');
        $result = $this->apiCategoryModel->updateOne(['id' => $form['id']], $form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'update category failed';
            return $this->return;
        }
        $this->return['lists'] = $form;
        return $this->return;
    }
    /**
     * todo:删除记录
     * @param $form
     * @return array
     */
    public function removeCategory($form)
    {
        $result = $this->apiCategoryModel->removeOne(['id' => $form['id']]);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'update category failed';
            return $this->return;
        }
        /* todo:删除接口详情 */
        $this->return['lists'] = $form;
        return $this->return;
    }
}
