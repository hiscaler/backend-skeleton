<?php

namespace app\modules\admin\modules\queue\controllers;

use app\models\Meta;
use app\modules\admin\extensions\BaseController;
use app\modules\admin\forms\DynamicForm;
use app\modules\admin\modules\post\models\PostRaw;
use app\modules\admin\modules\post\models\PostRawContent;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\queue\db\Queue;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * 队列管理
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
                        'actions' => ['index', 'update', 'delete'],
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
     * Lists all PostRaw models.
     *
     * @rbacDescription 资讯数据列表查看权限
     * @return mixed
     */
    public function actionIndex()
    {
        /* @var $queue Queue */
        $queue = Yii::$app->queue;
        $query = (new Query())->from($queue->tableName)
            ->orderBy(['id' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing PostRaw model.
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
        $newsContent = $model->newsContent ?: new PostRawContent();
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
     * Deletes an existing PostRaw model.
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
     * Finds the PostRaw model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return PostRaw the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PostRaw::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
