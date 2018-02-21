<?php

namespace app\modules\admin\controllers;

use app\models\FileUploadConfig;
use app\models\FileUploadConfigSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * 附件上传设定管理
 * Class FileUploadConfigsController
 *
 * @package app\modules\admin\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class FileUploadConfigsController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all FileUploadConfig models.
     *
     * @return mixed
     */
    /**
     * 文件上传设置数据列表
     *
     * @rbacDescription 文件上传设置数据列表查看权限
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FileUploadConfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new FileUploadConfig model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @rbacDescription 文件上传设置添加权限
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new FileUploadConfig();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing FileUploadConfig model.
     * If update is successful, the browser will be redirected to the 'index' page.
     *
     * @rbacDescription 文件上传设置更新权限
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing FileUploadConfig model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @rbacDescription 文件上传设置删除权限
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the FileUploadConfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return FileUploadConfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = FileUploadConfig::find()->where([
            'id' => (int) $id,
        ])->one();

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
