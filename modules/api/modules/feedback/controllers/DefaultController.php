<?php

namespace app\modules\api\modules\feedback\controllers;

use app\models\Meta;
use app\modules\admin\forms\DynamicForm;
use app\modules\api\extensions\UtilsHelper;
use app\modules\api\modules\feedback\models\Feedback;
use Yii;
use yii\base\InvalidArgumentException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\Inflector;
use yii\web\NotFoundHttpException;

/**
 * /api/feedback/default
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends Controller
{

    public $modelClass = 'app\modules\api\modules\feedback\models\Feedback';

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
        $where = [];
        $selectColumns = UtilsHelper::filterQuerySelectColumns(['t.id', 't.category_id', 'c.name AS category_name', 't.title', 't.username', 't.tel', 't.mobile_phone', 't.email', 't.ip', 't.picture', 't.message', 't.response_message', 't.response_datetime', 't.enabled', 't.created_at', 't.created_by', 't.updated_at', 't.updated_by'], $fields, ['category_name' => 't.category_id']);
        $query = (new ActiveQuery(Feedback::class))
            ->alias('t')
            ->select($selectColumns);

        if (in_array('c.name AS category_name', $selectColumns)) {
            $query->leftJoin('{{%category}} c', 't.category_id = c.id');
        }

        // 分类查询
        if ($category) {
            $where['t.category_id'] = (int) $category;
        }

        return $query->where($where)
            ->offset($offset)
            ->limit($limit)
            ->orderBy(['t.created_at' => SORT_DESC]);
    }

    /**
     * 列表（带翻页）
     *
     * @api GET /api/feedback/default?fields=:fields&category=:category&page=:page&pageSize=:pageSize
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
     * @api GET /api/feedback/default/list?fields=:fields&category=:category&offset=:offset&limit=:limit
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

    /**
     * 详情
     *
     * @api GET /api/feedback/default/view?id=:id
     * @param $id
     * @return null|static
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $model;
    }

    /**
     * 提交留言反馈
     *
     * @api POST /api/feedback/default/submit
     * @return Feedback|array
     * @throws \yii\base\ErrorException
     * @throws \yii\db\Exception
     */
    public function actionSubmit()
    {
        $model = new Feedback();
        $model->loadDefaultValues();

        $dynamicModel = new DynamicForm(Meta::getItems($model));

        $payload = [];
        foreach (Yii::$app->getRequest()->post() as $key => $value) {
            if (strpos($key, '_') !== false) {
                $key = Inflector::camel2id($key, '_');
            }
            $payload[$key] = $value;
        }
        if ($payload) {
            if (($model->load($payload, '') && $model->validate()) && (!$dynamicModel->attributes || ($dynamicModel->load($payload) && $dynamicModel->validate()))) {
                if ($model->save(false)) {
                    $dynamicModel->attributes && Meta::saveValues($model, $dynamicModel, true);

                    return $model;
                }
            }

            Yii::$app->getResponse()->setStatusCode(400);

            return $model->errors;
        } else {
            throw new InvalidArgumentException('未检测到提交的内容。');
        }
    }

    /**
     * @param $id
     * @return Feedback|null
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = Feedback::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('记录不存在。');
        }

        return $model;
    }

}
