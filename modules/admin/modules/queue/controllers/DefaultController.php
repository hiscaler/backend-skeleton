<?php

namespace app\modules\admin\modules\queue\controllers;

use app\modules\admin\extensions\BaseController;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * 队列管理
 *
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
                        'actions' => ['index', 'delete'],
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
     * @return mixed
     */
    public function actionIndex()
    {
        $query = (new Query())->from($this->queue->tableName)
            ->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'key' => 'id',
        ]);

        return $this->render('index', [
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
        \Yii::$app->getDb()->createCommand('DELETE FROM ' . $this->queue->tableName . ' WHERE [[id]] = :id', [':id' => $model['id']])->execute();

        return $this->redirect(Yii::$app->getRequest()->getReferrer());
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
