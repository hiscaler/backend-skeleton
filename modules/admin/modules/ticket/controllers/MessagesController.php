<?php

namespace app\modules\admin\modules\ticket\controllers;

use app\modules\admin\modules\ticket\models\TicketMessage;
use app\modules\admin\modules\ticket\models\TicketMessageSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * 工单消息管理
 *
 * @package app\modules\admin\modules\ticket\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class MessagesController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'view', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all TicketMessage models.
     *
     * @param $ticketId
     * @return mixed
     */
    public function actionIndex($ticketId = null)
    {
        $searchModel = new TicketMessageSearch();
        $searchModel->ticket_id = $ticketId;
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->queryParams);

        return $this->render('index', [
            'ticketId' => $ticketId,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TicketMessage model.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TicketMessage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @param $ticketId
     * @return mixed
     */
    public function actionCreate($ticketId)
    {
        $model = new TicketMessage();
        $model->ticket_id = (int) $ticketId;
        $model->type = TicketMessage::TYPE_CUSTOMER_SERVICE;

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'ticketId' => $model->ticket_id]);
        }

        return $this->render('create', [
            'ticketId' => $ticketId,
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TicketMessage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id, 'ticketId' => $model->ticket_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TicketMessage model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(['index', 'ticketId' => $model->ticket_id]);
    }

    /**
     * Finds the TicketMessage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return TicketMessage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TicketMessage::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    
}
