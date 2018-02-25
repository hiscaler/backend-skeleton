<?php

namespace app\modules\admin\controllers;

use app\models\Constant;
use app\models\Label;
use app\models\LabelSearch;
use Yii;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 推送位管理
 * Class LabelsController
 *
 * @package app\modules\admin\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class LabelsController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'delete', 'toggle'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'toggle' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Label models.
     *
     * @rbacDescription 推送位数据查看权限
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new LabelSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Label model.
     * If creation is successful, the browser will be redirected to the 'create' page.
     *
     * @rbacDescription 推送位添加权限
     * @return mixed
     */
    public function actionCreate($ordering = 1)
    {
        $model = new Label();
        $model->enabled = Constant::BOOLEAN_TRUE;
        $model->ordering = (int) $ordering;
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['create', 'ordering' => $model->ordering + 1]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Label model.
     * If update is successful, the browser will be redirected to the 'index' page.
     *
     * @rbacDescription 推送位更新权限
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
     * Deletes an existing Label model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @rbacDescription 推送位删除权限
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->frequency) {
            throw new NotAcceptableHttpException('有数据使用该推送位，禁止删除。');
        } else {
            $db = Yii::$app->getDb();
            $transaction = $db->beginTransaction();
            try {
                $cmd = $db->createCommand();
                $cmd->delete('{{%label}}', ['id' => $id])->execute();
                $cmd->delete('{{%entity_label}}', ['label_id' => $id])->execute();
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                throw new Exception($e->getMessage());
            }

            return $this->redirect(['index']);
        }
    }

    /**
     * 激活、禁止操作
     *
     * @rbacDescription 推送位激活、禁止操作权限
     * @return Response
     */
    public function actionToggle()
    {
        $id = Yii::$app->getRequest()->post('id');
        $db = Yii::$app->getDb();
        $value = $db->createCommand('SELECT [[enabled]] FROM {{%label}} WHERE [[id]] = :id', [':id' => (int) $id])->queryScalar();
        if ($value !== false) {
            $value = !$value;
            $now = time();
            $db->createCommand()->update('{{%label}}', ['enabled' => $value, 'updated_at' => $now, 'updated_by' => Yii::$app->getUser()->getId()], '[[id]] = :id', [':id' => (int) $id])->execute();
            $responseData = [
                'success' => true,
                'data' => [
                    'value' => $value,
                    'updatedAt' => Yii::$app->getFormatter()->asDate($now),
                    'updatedBy' => Yii::$app->getUser()->getIdentity()->username,
                ],
            ];
        } else {
            $responseData = [
                'success' => false,
                'error' => [
                    'message' => '数据有误',
                ],
            ];
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $responseData,
        ]);
    }

    /**
     * Finds the Label model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Label the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Label::find()->where([
            'id' => (int) $id,
        ])->one();

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
