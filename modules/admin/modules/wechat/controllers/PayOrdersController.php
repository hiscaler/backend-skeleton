<?php

namespace app\modules\admin\modules\wechat\controllers;

use app\modules\admin\modules\wechat\models\PayOrder;
use app\modules\admin\modules\wechat\models\PayOrderSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * 企业付款订单管理
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class PayOrdersController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all PayOrder models.
     *
     * @rbacDescription 企业付款订单列表数据查看权限
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PayOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PayOrder model.
     *
     * @rbacDescription 企业付款订单详情查看权限
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
     * Creates a new PayOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @rbacDescription 企业付款订单添加权限
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PayOrder();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PayOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @rbacDescription 企业付款订单更新权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PayOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @rbacDescription 企业付款订单删除权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PayOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return PayOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PayOrder::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
