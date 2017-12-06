<?php

namespace app\modules\admin\controllers;

use app\models\MemberSearch;
use app\models\User;
use app\models\UserCreditLog;
use app\modules\admin\forms\ChangePasswordForm;
use app\modules\admin\forms\CreditForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * 会员管理
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class MembersController extends ShopController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'view', 'change-password', 'add-credits'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MemberSearch();
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

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
     * 会员详情
     *
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $creditLogsDataProvider = new ActiveDataProvider([
            'query' => UserCreditLog::find()->where(['user_id' => $model['id']]),
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ]
        ]);

        return $this->render('view', [
            'model' => $model,
            'creditLogsDataProvider' => $creditLogsDataProvider
        ]);
    }

    /**
     * 添加积分
     *
     * @param integer $id
     * @return mixed
     */
    public function actionAddCredits($id)
    {
        $member = $this->findModel($id);
        $model = new CreditForm();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            UserCreditLog::add($member['id'], UserCreditLog::OPERATION_MANUAL, $model['credits'], null, $model['remark']);

            return $this->redirect(['members/view', 'id' => $member['id']]);
        }

        return $this->render('add-credits', [
            'model' => $model,
        ]);
    }

    /**
     * 修改密码
     *
     * @return mixed
     */
    public function actionChangePassword($id)
    {
        $user = $this->findModel($id);
        $model = new ChangePasswordForm();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $user->setPassword($model->password);
            if ($user->save()) {
                Yii::$app->getSession()->setFlash('notice', "用户 {$user->username} 密码修改成功，请通知用户下次登录使用新的密码。");

                return $this->redirect(['index']);
            }
        }

        return $this->render('change-password', [
            'user' => $user,
            'model' => $model,
        ]);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = User::find()->where(['id' => (int) $id])->one();

        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
