<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\Code;
use Illuminate\Http\Request;

class InterfaceCategoryService extends BaseService
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
     * 获取分类列表
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
     * 数据添加
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
            $form['path'] = $parent_result->path . '-' . $id;
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
     * 数据更新
     * @param $form
     * @return array
     */
    public function updateCategory($form)
    {
        $parent_result = $this->apiCategoryModel->getOne(['id' => $form['pid']], ['path']);
        $form['path'] = !empty($parent_result->path) ? $parent_result->path . '-' . $form['id'] : $form['id'];
        $form['level'] = substr_count($form['path'], '-');
        $result = $this->apiCategoryModel->updateOne(['id' => $form['id']], $form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'Error update category';
            return $this->return;
        }
        $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * 删除记录
     * @param $form
     * @return array
     */
    public function removeCategory($form)
    {
        $result = $this->apiCategoryModel->removeOne(['id' => $form['id']]);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'Error delete category';
            return $this->return;
        }
        /* 删除接口详情 */
        $this->return['lists'] = $form;
        return $this->return;
    }

}
