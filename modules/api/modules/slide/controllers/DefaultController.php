<?php

namespace app\modules\api\modules\slide\controllers;

use app\modules\api\extensions\BaseController;
use app\modules\api\extensions\UtilsHelper;
use app\modules\api\models\Constant;
use app\modules\api\modules\slide\models\Slide;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * /api/slide/default
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends BaseController
{

    public $modelClass = 'app\modules\api\modules\slide\models\Slide';

    /**
     * 搜索条件解析
     *
     * @param null $fields
     * @param null $category
     * @param null $offset
     * @param null $limit
     * @return \yii\db\Query
     */
    private function parseQuery($fields = null, $category = null, $offset = null, $limit = null)
    {
        $where = ['t.enabled' => Constant::BOOLEAN_TRUE];
        $selectColumns = UtilsHelper::filterQuerySelectColumns(['t.id', 't.category_id', 'c.name AS category_name', 't.title', 't.url', 't.url_open_target', 't.picture_path'], $fields, ['category_name' => 't.category_id']);
        $query = (new ActiveQuery(Slide::class))
            ->alias('t')
            ->select($selectColumns);

        if (in_array('c.name AS category_name', $selectColumns)) {
            $query->leftJoin('{{%category}} c', 't.category_id = c.id');
        }

        // 提交处理
        if ($category) {
            $where['t.category_id'] = (int) $category;
        }

        return $query->where($where)
            ->offset($offset)
            ->limit($limit)
            ->orderBy(['t.ordering' => SORT_ASC]);
    }

    /**
     * 列表（带翻页）
     *
     * @api GET /api/slide/default?fields=:fields&category=:category&page=:page&pageSize=:pageSize
     * @param null $category
     * @param int $page
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function actionIndex($category = null, $page = 1, $pageSize = 20)
    {
        return new ActiveDataProvider([
            'query' => $this->parseQuery(Yii::$app->getRequest()->get('fields'), $category, $page, $pageSize),
            'pagination' => [
                'page' => (int) $page - 1,
                'pageSize' => (int) $pageSize ?: 20
            ]
        ]);
    }

    /**
     * 列表（不带翻页）
     *
     * @api GET /api/slide/default/list?fields=:fields&category=:category&offset=:offset&limit=:limit
     * @param null $fields
     * @param null $category
     * @param int $offset
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function actionList($fields = null, $category = null, $offset = 0, $limit = 10)
    {
        return new ActiveDataProvider([
            'query' => $this->parseQuery($fields, $category, $offset, $limit),
            'pagination' => false
        ]);
    }

}
