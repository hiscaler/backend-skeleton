<?php

namespace app\modules\admin\modules\queue\controllers;

use app\modules\admin\extensions\BaseController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 队列管理
 *
 * @package app\modules\admin\modules\queue\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends BaseController
{

    /* @var $queue yii\queue\db\Queue */
    protected $queue;

    public function init()
    {
        parent::init();
        $this->queue = Yii::$app->queue;
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'delete', 'batch-delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 查看队列数据
     *
     * @rbacDescription 队列任务数据列表查看权限
     * @param null $channel
     * @return mixed
     */
    public function actionIndex($channel = null)
    {
        $where = [];
        if ($channel = trim($channel)) {
            $where['channel'] = $channel;
        }
        $query = (new Query())
            ->from($this->queue->tableName)
            ->where($where)
            ->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'key' => 'id',
        ]);

        return $this->render('index', [
            'channel' => $channel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 删除队列任务
     *
     * @rbacDescription 队列任务删除权限
     * @param $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        Yii::$app->getDb()->createCommand()
            ->delete($this->queue->tableName, ['id' => $model['id']])
            ->execute();

        return $this->redirect(Yii::$app->getRequest()->getReferrer());
    }

    /**
     * 批量删除
     *
     * @rbacDescription 批量删除队列数据权限
     * @return Response
     * @throws \yii\db\Exception
     */
    public function actionBatchDelete()
    {
        $success = false;
        $errorMessage = null;
        $ids = trim(Yii::$app->getRequest()->post('ids'));
        $ids = array_filter(array_unique(explode(',', $ids)));
        if ($ids) {
            Yii::$app->getDb()
                ->createCommand()
                ->delete($this->queue->tableName, ['id' => $ids])
                ->execute();

            $success = true;
        } else {
            $errorMessage = '请选择您要删除的数据。';
        }

        $responseBody = ['success' => $success];
        if (!$success) {
            $responseBody['error']['message'] = $errorMessage;
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $responseBody,
        ]);
    }

    /**
     * 返回某一队列任务详情
     *
     * @param $id
     * @return array|bool
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        $model = (new Query())
            ->from($this->queue->tableName)
            ->where(['id' => (int) $id])
            ->one();
        if ($model !== false) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
