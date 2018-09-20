<?php

namespace app\modules\admin\modules\article\controllers;

use app\models\Meta;
use app\modules\admin\extensions\BaseController;
use app\modules\admin\forms\DynamicForm;
use app\modules\admin\modules\article\models\Article;
use app\modules\admin\modules\article\models\ArticleSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * 单文章管理
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
                        'actions' => ['index', 'create', 'update', 'delete', 'view'],
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
     * 单文章列表
     *
     * @rbacDescription 单文章列表查看权限
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ArticleSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Article model.
     *
     * @rbacDescription 单文章详情查看权限
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
     * 单文章添加
     *
     * @rbacDescription 单文章添加权限
     * @return mixed
     * @throws \yii\base\ErrorException
     * @throws \yii\db\Exception
     */
    public function actionCreate()
    {
        $model = new Article();
        $dynamicModel = new DynamicForm(Meta::getItems($model));

        $post = Yii::$app->getRequest()->post();

        if (($model->load($post) && $model->validate()) && (!$dynamicModel->attributes || ($dynamicModel->load($post) && $dynamicModel->validate()))) {
            $model->save(false);
            $dynamicModel->attributes && Meta::saveValues($model, $dynamicModel, true);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'dynamicModel' => $dynamicModel,
        ]);
    }

    /**
     * 单文章更新
     *
     * @rbacDescription 单文章更新权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \yii\base\ErrorException
     * @throws \yii\db\Exception
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $dynamicModel = new DynamicForm(Meta::getItems($model));

        $post = Yii::$app->getRequest()->post();
        if (($model->load($post) && $model->validate()) && (!$dynamicModel->attributes || ($dynamicModel->load($post) && $dynamicModel->validate()))) {
            $model->save(false);
            $dynamicModel->attributes && Meta::saveValues($model, $dynamicModel, true);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'dynamicModel' => $dynamicModel,
        ]);
    }

    /**
     * 单文章删除
     *
     * @rbacDescription 单文章删除权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Article::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
