<?php

namespace app\modules\admin\modules\notice\controllers;

use app\models\Category;
use app\models\Meta;
use app\modules\admin\extensions\BaseController;
use app\modules\admin\forms\DynamicForm;
use app\modules\admin\modules\notice\models\Notice;
use app\modules\admin\modules\notice\models\NoticeSearch;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 通知管理
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
     * 案例列表
     *
     * @rbacDescription 案例列表查看权限
     * @return mixed
     * @throws \yii\db\Exception
     */
    public function actionIndex()
    {
        $searchModel = new NoticeSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams());

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'categories' => Category::tree('classicCase.module.category', Category::RETURN_TYPE_PRIVATE)
        ]);
    }

    /**
     * 通知详情
     *
     * @rbacDescription 通知详情查看权限
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
     * 案例添加
     *
     * @rbacDescription 案例添加权限
     * @return mixed
     * @throws HttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        $model = new Notice();
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
     * 案例更新
     *
     * @rbacDescription 案例更新权限
     * @param integer $id
     * @return mixed
     * @throws HttpException
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \yii\db\Exception
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->view_permission == Notice::VIEW_PERMISSION_SPECIAL) {
            $t = \Yii::$app->getDb()->createCommand('SELECT [[m.username]] FROM {{%notice_permission}} t LEFT JOIN {{%member}} AS m ON [[t.xid]] = [[m.id]] WHERE [[notice_id]] = :noticeId', [':noticeId' => $model->id])->queryColumn();
            $model->view_member_username_list = implode(',', $t);
        } elseif ($model->view_permission == Notice::VIEW_PERMISSION_BY_MEMBER_TYPE) {
            $t = \Yii::$app->getDb()->createCommand('SELECT [[xid]] FROM {{%notice_permission}} WHERE [[notice_id]] = :noticeId', [':noticeId' => $model->id])->queryColumn();
            $model->view_member_type_list = $t;
        }
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
     * 案例删除
     *
     * @rbacDescription 案例删除权限
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
     * 案例激活状态修改
     *
     * @rbacDescription 案例激活状态修改权限
     * @return Response
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionToggle()
    {
        $id = Yii::$app->getRequest()->post('id');
        $db = Yii::$app->getDb();
        $value = $db->createCommand('SELECT [[enabled]] FROM {{%notice}} WHERE [[id]] = :id', [':id' => (int) $id])->queryScalar();
        if ($value !== null) {
            $value = !$value;
            $now = time();
            $db->createCommand()->update('{{%notice}}', ['enabled' => $value, 'updated_by' => Yii::$app->getUser()->getId(), 'updated_at' => $now], '[[id]] = :id', [':id' => (int) $id])->execute();
            $responseBody = [
                'success' => true,
                'data' => [
                    'value' => $value,
                    'updatedAt' => Yii::$app->getFormatter()->asDate($now),
                    'updatedBy' => Yii::$app->getUser()->getIdentity()->username,
                ],
            ];
        } else {
            $responseBody = [
                'success' => false,
                'error' => [
                    'message' => '数据有误',
                ],
            ];
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $responseBody,
        ]);
    }

    /**
     * Finds the ClassicCase model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Notice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Notice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
