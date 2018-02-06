<?php

namespace app\modules\api\modules\link\controllers;

use app\modules\api\extensions\BaseController;
use app\modules\api\models\Constant;
use app\modules\api\modules\link\models\Link;
use yii\data\ActiveDataProvider;
use app\modules\api\extensions\UtilsHelper;
use Yii;
use yii\db\ActiveQuery;

/**
 * /api/link/default
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends BaseController
{

    public $modelClass = 'app\modules\api\modules\link\models\Link';

    /**
     * 搜索条件解析
     *
     * @param null $fields
     * @param null $category
     * @param null $type
     * @param null $offset
     * @param null $limit
     * @return \yii\db\Query
     */
    private function parseQuery($fields = null, $category = null, $type = null, $offset = null, $limit = null)
    {
        $where = ['t.enabled' => Constant::BOOLEAN_TRUE];
        $selectColumns = UtilsHelper::filterQuerySelectColumns(['t.id', 't.category_id', 'c.name AS category_name', 't.type', 't.title', 't.description', 't.url', 't.url_open_target', 't.logo'], $fields, ['category_name' => 't.category_id']);
        $query = (new ActiveQuery(Link::className()))
            ->alias('t')
            ->select($selectColumns);

        if (in_array('c.name AS category_name', $selectColumns)) {
            $query->leftJoin('{{%category}} c', 't.category_id = c.id');
        }

        // 提交处理
        if ($category) {
            $where['t.category_id'] = (int) $category;
        }
        if ($type) {
            switch (strtolower($type)) {
                case 'text':
                    $where['t.type'] = Link::TYPE_TEXT;
                    break;

                case 'picture':
                    $where['t.type'] = Link::TYPE_PICTURE;
                    break;

                default:
                    $where = '0 = 1';
                    break;
            }
        }

        return $query->where($where)
            ->offset($offset)
            ->limit($limit)
            ->orderBy(['t.ordering' => SORT_ASC]);
    }

    /**
     * 列表（带翻页）
     *
     * @api GET /api/link/default?fields=:fields&category=:catgory&type=:type&page=:page&pageSize=:pageSize
     * @param null $category
     * @param null $type
     * @param int $page
     * @param int $pageSize
     * @return ActiveDataProvider
     */
    public function actionIndex($category = null, $type = null, $page = 1, $pageSize = 20)
    {
        return new ActiveDataProvider([
            'query' => $this->parseQuery(Yii::$app->getRequest()->get('fields'), $category, $type, $page, $pageSize),
            'pagination' => [
                'page' => (int) $page - 1,
                'pageSize' => (int) $pageSize ?: 20
            ]
        ]);
    }

    /**
     * 列表（不带翻页）
     *
     * @api GET /api/link/default/list?fields=:fields&category=:catgory&type=:type&offset=:offset&limit=:limit
     * @param null $fields
     * @param null $category
     * @param null $type
     * @param int $offset
     * @param int $limit
     * @return ActiveDataProvider
     */
    public function actionList($fields = null, $category = null, $type = null, $offset = 0, $limit = 10)
    {
        return new ActiveDataProvider([
            'query' => $this->parseQuery($fields, $category, $type, $offset, $limit),
            'pagination' => false
        ]);
    }

}
