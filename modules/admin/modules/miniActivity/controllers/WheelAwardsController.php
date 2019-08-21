<?php

namespace app\modules\admin\modules\miniActivity\controllers;

use app\modules\admin\modules\miniActivity\models\Wheel;
use app\modules\admin\modules\miniActivity\models\WheelAward;
use app\modules\admin\modules\miniActivity\models\WheelAwardSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * AwardsController implements the CRUD actions for WheelAward model.
 */
class WheelAwardsController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all WheelAward models.
     *
     * @return mixed
     */
    public function actionIndex($wheelId)
    {
        $wheel = $this->findWheelModel($wheelId);
        $searchModel = new WheelAwardSearch();
        $searchModel->wheel_id = (int) $wheelId;
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());

        return $this->render('index', [
            'wheel' => $wheel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WheelAward model.
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
     * Creates a new WheelAward model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @param $wheelId
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate($wheelId)
    {
        $wheel = $this->findWheelModel($wheelId);
        $model = new WheelAward();
        $model->wheel_id = $wheel['id'];
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing WheelAward model.
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
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing WheelAward model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
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
     * Finds the WheelAward model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return WheelAward the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WheelAward::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findWheelModel($id)
    {
        if (($model = Wheel::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
