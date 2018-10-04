<?php

namespace app\modules\admin\modules\feedback\controllers;

use app\models\Category;
use app\modules\admin\modules\feedback\forms\ReplyForm;
use Yii;
use app\modules\admin\modules\feedback\models\Feedback;
use app\modules\admin\modules\feedback\models\FeedbackSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * 留言反馈数据管理
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends Controller
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
                        'actions' => ['index', 'view', 'reply', 'delete'],
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
     * 留言反馈消息列表
     *
     * @rbacDescription 留言反馈消息列表查看权限
     * @return mixed
     * @throws \yii\db\Exception
     */
    public function actionIndex()
    {
        $searchModel = new FeedbackSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'categories' => Category::tree('feedback.module.category', Category::RETURN_TYPE_PRIVATE)
        ]);
    }

    /**
     * 留言反馈消息详情
     *
     * @rbacDescription 留言反馈消息详情查看权限
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
     * 回复反馈消息
     *
     * @rbacDescription 回复反馈消息权限
     *
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionReply($id)
    {
        $feedback = $this->findModel($id);
        $model = new ReplyForm();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $feedback->response_message = $model->message;
            $feedback->response_datetime = time();
            $feedback->save(false);

            return $this->redirect(['index']);
        }

        return $this->render('reply', [
            'feedback' => $feedback,
            'model' => $model,
        ]);
    }

    /**
     * 留言反馈删除
     *
     * @rbacDescription 留言反馈删除权限
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
     * Finds the Feedback model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Feedback the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Feedback::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
