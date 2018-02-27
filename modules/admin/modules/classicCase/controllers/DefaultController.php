<?php

namespace app\modules\admin\modules\classicCase\controllers;

use app\models\Category;
use app\models\Meta;
use app\modules\admin\extensions\BaseController;
use app\modules\admin\forms\DynamicForm;
use Yii;
use app\modules\admin\modules\classicCase\models\ClassicCase;
use app\modules\admin\modules\classicCase\models\ClassicCaseSearch;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * 案例管理
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends BaseController
{

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
                        'actions' => ['index', 'create', 'update', 'view', 'delete', 'toggle'],
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
     * Lists all ClassicCase models.
     *
     * @rbacDescription 案例列表查看权限
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ClassicCaseSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'categories' => Category::tree('classicCase.module.category', Category::RETURN_TYPE_PRIVATE)
        ]);
    }

    /**
     * Displays a single ClassicCase model.
     *
     * @rbacDescription 案例详情查看权限
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
     * Creates a new ClassicCase model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @rbacDescription 案例添加权限
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ClassicCase();
        $model->loadDefaultValues();
        $model->published_at = Yii::$app->getFormatter()->asDatetime(time());
        $dynamicModel = new DynamicForm(Meta::getItems($model));

        $post = Yii::$app->getRequest()->post();

        if (($model->load($post) && $model->validate()) && (!$dynamicModel->attributes || ($dynamicModel->load($post) && $dynamicModel->validate()))) {
            $transaction = Yii::$app->getDb()->beginTransaction();
            try {
                $model->save(false);
                $dynamicModel->attributes && Meta::saveValues($model, $dynamicModel, true);
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollback();
                throw new HttpException(500, $e->getMessage());
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'dynamicModel' => $dynamicModel,
        ]);
    }

    /**
     * Updates an existing ClassicCase model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @rbacDescription 案例更新权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $dynamicModel = new DynamicForm(Meta::getItems($model));

        $post = Yii::$app->getRequest()->post();
        if (($model->load($post) && $model->validate()) && (!$dynamicModel->attributes || ($dynamicModel->load($post) && $dynamicModel->validate()))) {
            $transaction = Yii::$app->getDb()->beginTransaction();
            try {
                $model->save(false);
                $dynamicModel->attributes && Meta::saveValues($model, $dynamicModel, true);
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollback();
                throw new HttpException(500, $e->getMessage());
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'dynamicModel' => $dynamicModel,
        ]);
    }

    /**
     * Deletes an existing ClassicCase model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @rbacDescription 案例删除权限
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
     * Toggle enabled
     *
     * @rbacDescription 案例激活状态修改权限
     * @return Response
     */
    public function actionToggle()
    {
        $id = Yii::$app->getRequest()->post('id');
        $db = Yii::$app->getDb();
        $value = $db->createCommand('SELECT [[enabled]] FROM {{%classic_case}} WHERE [[id]] = :id', [':id' => (int) $id])->queryScalar();
        if ($value !== null) {
            $value = !$value;
            $now = time();
            $db->createCommand()->update('{{%classic_case}}', ['enabled' => $value, 'updated_by' => Yii::$app->getUser()->getId(), 'updated_at' => $now], '[[id]] = :id', [':id' => (int) $id])->execute();
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
     * Finds the ClassicCase model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return ClassicCase the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ClassicCase::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
