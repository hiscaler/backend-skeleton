<?php

namespace app\modules\api\modules\notice\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\modules\notice\models\Notice;
use app\modules\api\modules\notice\models\NoticeSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * /api/notice/default
 *
 * @package app\modules\api\modules\notice\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends ActiveController
{

    public $modelClass = Notice::class;

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                    'update' => ['PUT', 'PATCH'],
                    'read' => ['POST'],
                    'delete' => ['DELETE'],
                    '*' => ['GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create', 'update'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'view', 'read'],
                        'allow' => true,
                        'roles' => ['@', '?'],
                    ],
                ],
            ],
        ]);

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['delete']);
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    /**
     * @return \yii\data\ActiveDataProvider
     * @throws \Throwable
     */
    public function prepareDataProvider()
    {
        $search = new NoticeSearch();

        return $search->search(Yii::$app->getRequest()->getQueryParams());
    }

    /**
     * 设置为已读状态
     *
     * @param $id
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionRead($id)
    {
        $model = $this->findModel($id);
        $memberId = Yii::$app->getUser()->getId();
        $db = Yii::$app->getDb();
        $n = $db->createCommand('SELECT COUNT(*) FROM {{%notice_view}} WHERE [[notice_id]] = :noticeId AND [[member_id]] = :memberId', [
            ':noticeId' => $model->id,
            ':memberId' => $memberId,
        ])->queryScalar();
        if (!$n) {
            $db->createCommand()->insert('{{%notice_view}}', [
                'notice_id' => $model->id,
                'member_id' => $memberId,
                'view_datetime' => time(),
            ])->execute();
        }
        Yii::$app->getResponse()->setStatusCode(200);
    }

    /**
     * @param $id
     * @return Notice|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Notice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
