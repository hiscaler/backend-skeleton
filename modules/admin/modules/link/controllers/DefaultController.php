<?php

namespace app\modules\admin\modules\link\controllers;

use app\models\Category;
use app\modules\admin\extensions\BaseController;
use app\modules\admin\modules\link\models\Link;
use app\modules\admin\modules\link\models\LinkSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 友情链接管理
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
     * 链接列表
     *
     * @rbacDescription 链接列表查看权限
     * @return mixed
     * @throws \yii\db\Exception
     */
    public function actionIndex()
    {
        $searchModel = new LinkSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'categories' => Category::tree('link.module.category', Category::RETURN_TYPE_PRIVATE)
        ]);
    }

    /**
     * 链接详情
     *
     * @rbacDescription 链接详情查看权限
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
     * 添加链接
     *
     * @rbacDescription 链接添加权限
     * @param null $category
     * @param int $type
     * @param string $urlOpenTarget
     * @param int $ordering
     * @return string|Response
     */
    public function actionCreate($category = null, $type = Link::TYPE_TEXT, $urlOpenTarget = Link::URL_OPEN_TARGET_BLANK, $ordering = 1)
    {
        $model = new Link();
        $model->loadDefaultValues();
        $category && $model->category_id = (int) $category;
        if (!isset(Link::typeOptions()[$type])) {
            $type = Link::TYPE_TEXT;
        }
        $model->type = $type;
        if (!isset(Link::urlOpenTargetOptions()[$urlOpenTarget])) {
            $urlOpenTarget = Link::URL_OPEN_TARGET_BLANK;
        }
        $model->url_open_target = $urlOpenTarget;

        $model->ordering = (int) $ordering;

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            return $this->redirect(['create', 'category' => $model->category_id, 'type' => $model->type, 'urlOpenTarget' => $model->url_open_target, 'ordering' => $model->ordering + 1]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 更新链接
     *
     * @rbacDescription 链接更新权限
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
     * 删除链接
     *
     * @rbacDescription 链接删除权限
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->setScenario($model::SCENARIO_DELETE);
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * 切换是否激活开关
     *
     * @rbacDescription 链接激活状态修改权限
     * @return Response
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionToggle()
    {
        $id = Yii::$app->getRequest()->post('id');
        $db = Yii::$app->getDb();
        $value = $db->createCommand('SELECT [[enabled]] FROM {{%link}} WHERE [[id]] = :id', [':id' => (int) $id])->queryScalar();
        if ($value !== null) {
            $value = !$value;
            $now = time();
            $db->createCommand()->update('{{%link}}', ['enabled' => $value, 'updated_at' => $now, 'updated_by' => Yii::$app->getUser()->getId()], ['id' => (int) $id])->execute();
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
     * Finds the Link model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Link the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Link::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

}
