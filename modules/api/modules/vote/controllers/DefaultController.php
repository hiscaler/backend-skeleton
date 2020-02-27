<?php

namespace app\modules\api\modules\vote\controllers;

use app\modules\api\extensions\BaseController;
use app\modules\api\extensions\UtilsHelper;
use app\modules\api\models\Constant;
use app\modules\api\modules\vote\models\Vote;
use Yii;
use yii\base\Exception;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;

/**
 * /api/vote/default
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends BaseController
{

    public $modelClass = 'app\modules\api\modules\vote\models\Vote';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'voting' => ['POST'],
                    '*' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * 解析查询条件
     *
     * @param null $fields
     * @param null $category
     * @param null $orderBy
     * @param null $offset
     * @param null $limit
     * @return ActiveQuery
     */
    private function parserQuery($fields = null, $category = null, $orderBy = null, $offset = null, $limit = null)
    {
        // Basic condition
        $where = [
            't.enabled' => Constant::BOOLEAN_TRUE,
        ];
        // Category condition
        if (!empty($category)) {
            if (strpos($category, ',') === false) {
                $where['t.category_id'] = (int) $category;
            } else {
                $where['t.category_id'] = explode(',', $category);
            }
        }

        $selectColumns = UtilsHelper::filterQuerySelectColumns(['t.id', 't.category_id', 'c.name AS category_name', 't.title', 't.description', 't.begin_datetime', 't.end_datetime', 't.total_votes_count', 't.allow_anonymous', 't.allow_view_results', 't.allow_multiple_choice', 't.interval_seconds', 't.items', 't.ordering', 't.created_at', 't.updated_at'], $fields);
        $query = (new ActiveQuery(Vote::class))
            ->alias('t')
            ->select($selectColumns)
            ->where($where)
            ->offset($offset)
            ->limit($limit);

        if (in_array('c.name AS category_name', $selectColumns)) {
            $query->leftJoin('{{%category}} c', '[[t.category_id]] = [[c.id]]');
        }

        // Order By
        $orderByColumns = [];
        if (!empty($orderBy)) {
            $orderByColumnLimit = ['id', 'ordering', 'votesCount', 'createdAt', 'updatedAt']; // Supported order by column names
            foreach (explode(',', trim($orderBy)) as $string) {
                if (!empty($string)) {
                    $string = explode('.', $string);
                    if (in_array($string[0], $orderByColumnLimit)) {
                        $orderByColumns[Inflector::camel2id($string[0], '_')] = isset($string[1]) && $string[1] == 'asc' ? SORT_ASC : SORT_DESC;
                    }
                }
            }
        }

        return $query->orderBy($orderByColumns ?: ['t.ordering' => SORT_ASC]);
    }

    /**
     * 投票数据列表
     *
     * @param null $category
     * @param null $orderBy
     * @param int $page
     * @param int $pageSize
     * @return ActiveDataProvider
     * @api /api/article/default/index
     */
    public function actionIndex($category = null, $orderBy = null, $page = 1, $pageSize = 20)
    {
        return new ActiveDataProvider([
            'query' => $this->parserQuery(null, $category, $orderBy, null, null),
            'pagination' => [
                'page' => (int) $page - 1,
                'pageSize' => (int) $pageSize ?: 20
            ]
        ]);
    }

    public function actionList($fields = null, $category = null, $orderBy = null, $offset = 0, $limit = 10)
    {
        return new ActiveDataProvider([
            'query' => $this->parserQuery($fields, $category, $orderBy, $offset, $limit),
            'pagination' => false
        ]);
    }

    /**
     * 投票
     *
     * @param $id
     * @return Vote|array|null|\yii\db\ActiveRecord
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionVoting($id)
    {
        $optionId = (int) Yii::$app->getRequest()->post('optionId');
        if (empty($optionId)) {
            throw new BadRequestHttpException('无效的 optionId 参数值。');
        }
        $db = Yii::$app->getDb();
        $vote = $this->findModel($id);
        if ($vote) {
            $now = time();
            if ($vote['begin_datetime'] > $now) {
                throw new Exception('投票尚未开始。');
            } elseif ($vote['end_datetime'] < $now) {
                throw new Exception('投票已结束。');
            } else {
                $options = $vote->options;
                $exist = false;
                foreach ($options as $option) {
                    if ($option['id'] == $optionId) {
                        $exist = true;
                        break;
                    }
                }
                if (!$exist) {
                    throw new Exception('投票选项错误。');
                }

                $ip = Yii::$app->getRequest()->getUserIP();
                if ($vote['interval_seconds']) {
                    $lastPostDatetime = $db->createCommand('SELECT [[post_datetime]] FROM {{%vote_log}} WHERE [[vote_id]] = :voteId AND [[ip_address]] = :ip ORDER BY [[post_datetime]] DESC', [':voteId' => $vote['id'], ':ip' => $ip])->queryScalar();
                    if ($lastPostDatetime && ($lastPostDatetime + $vote['interval_seconds']) > $now) {
                        throw new Exception('投票间隔时间过短，请等待 ' . Yii::$app->getFormatter()->asDuration($lastPostDatetime + $vote['interval_seconds'] - $now, ' ') . '后再投票。');
                    }
                }

                $cmd = $db->createCommand();
                $cmd->insert('{{%vote_log}}', [
                    'vote_id' => $vote['id'],
                    'option_id' => $optionId,
                    'ip_address' => $ip,
                    'post_datetime' => $now,
                    'member_id' => \Yii::$app->getUser()->getId() ?: 0,
                ])->execute();

                $db->createCommand('UPDATE {{%vote_option}} SET [[votes_count]] = [[votes_count]] + 1 WHERE [[id]] = :id', [':id' => $optionId])->execute();
                $db->createCommand('UPDATE {{%vote}} SET [[total_votes_count]] = [[total_votes_count]] + 1 WHERE [[id]] = :id', [':id' => $vote['id']])->execute();

                $vote->refresh();

                return $vote;
            }
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    /**
     * 活动详情
     *
     * @param $id
     * @return Vote|array|null|\yii\db\ActiveRecord
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $model;
    }

    /**
     * @param $id
     * @return Vote|array|\yii\db\ActiveRecord|null
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = Vote::find()->where(['id' => (int) $id, 'enabled' => Constant::BOOLEAN_TRUE])->one();
        if (!$model) {
            throw new NotFoundHttpException('数据不存在。');
        }

        return $model;
    }

}
