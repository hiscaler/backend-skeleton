<?php

namespace app\modules\admin\modules\news\controllers;

use app\models\Category;
use app\models\Meta;
use app\modules\admin\extensions\BaseController;
use app\modules\admin\forms\DynamicForm;
use app\modules\admin\modules\news\models\News;
use app\modules\admin\modules\news\models\NewsContent;
use app\modules\admin\modules\news\models\NewsSearch;
use Yii;
use yii\base\Model;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 资讯管理
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
                        'actions' => ['index', 'create', 'update', 'view', 'delete', 'toggle', 'toggle-comment'],
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
     * Lists all News models.
     *
     * @rbacDescription 资讯数据列表查看权限
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NewsSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'categories' => Category::tree('news.module.category', Category::RETURN_TYPE_PRIVATE),
        ]);
    }

    /**
     * Displays a single News model.
     *
     * @rbacDescription 资讯详情查看权限
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
     * Creates a new News model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @rbacDescription 资讯添加权限
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new News();
        $model->loadDefaultValues();
        $model->published_at = Yii::$app->getFormatter()->asDatetime(time());
        $newsContent = new NewsContent();
        $dynamicModel = new DynamicForm(Meta::getItems($model));

        $post = Yii::$app->getRequest()->post();

        if (($model->load($post) && $newsContent->load($post) && Model::validateMultiple([$model, $newsContent])) && (!$dynamicModel->attributes || ($dynamicModel->load($post) && $dynamicModel->validate()))) {
            $transaction = Yii::$app->getDb()->beginTransaction();
            try {
                $model->save(false);
                $model->saveContent($newsContent); // 保存资讯正文
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
            'newsContent' => $newsContent,
            'dynamicModel' => $dynamicModel,
        ]);
    }

    /**
     * Updates an existing News model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @rbacDescription 资讯更新权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $newsContent = $model->newsContent ?: new NewsContent();
        $dynamicModel = new DynamicForm(Meta::getItems($model));

        $post = Yii::$app->getRequest()->post();
        if (($model->load($post) && $newsContent->load($post) && Model::validateMultiple([$model, $newsContent])) && (!$dynamicModel->attributes || ($dynamicModel->load($post) && $dynamicModel->validate()))) {
            $transaction = Yii::$app->getDb()->beginTransaction();
            try {
                $model->save(false);
                $model->saveContent($newsContent); // 保存资讯正文
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
            'newsContent' => $newsContent,
            'dynamicModel' => $dynamicModel,
        ]);
    }

    /**
     * Deletes an existing News model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @rbacDescription 资讯删除权限
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
     * @rbacDescription 资讯激活状态修改权限
     * @return Response
     */
    public function actionToggle()
    {
        $id = Yii::$app->getRequest()->post('id');
        $db = Yii::$app->getDb();
        $value = $db->createCommand('SELECT [[enabled]] FROM {{%news}} WHERE [[id]] = :id', [':id' => (int) $id])->queryScalar();
        if ($value !== null) {
            $value = !$value;
            $now = time();
            $db->createCommand()->update('{{%news}}', ['enabled' => $value, 'updated_by' => Yii::$app->getUser()->getId(), 'updated_at' => $now], '[[id]] = :id', [':id' => (int) $id])->execute();
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
     * Toggle enabled comment function
     *
     * @rbacDescription 资讯评论激活状态修改权限
     * @return Response
     */
    public function actionToggleComment()
    {
        $id = Yii::$app->getRequest()->post('id');
        $db = Yii::$app->getDb();
        $value = $db->createCommand('SELECT [[enabled_comment]] FROM {{%news}} WHERE [[id]] = :id', [':id' => (int) $id])->queryScalar();
        if ($value !== null) {
            $value = !$value;
            $now = time();
            $db->createCommand()->update('{{%news}}', ['enabled_comment' => $value, 'updated_by' => Yii::$app->getUser()->getId(), 'updated_at' => $now], 'id = :id', [':id' => (int) $id])->execute();
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
     * Finds the News model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return News the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = News::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
