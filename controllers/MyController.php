<?php

namespace app\controllers;

use app\forms\ChangeMyPasswordForm;
use app\models\Member;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

class MyController extends Controller
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
                        'actions' => ['index', 'change-password'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $identity = $this->findModel();

        return $this->render('index', [
            'identity' => $identity,
        ]);
    }

    /**
     * 修改密码
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionChangePassword()
    {
        $user = $this->findModel();
        $model = new ChangeMyPasswordForm();
        $model->username = $user->username;

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $user->setPassword($model->password);
            if ($user->save(false)) {
                Yii::$app->getSession()->setFlash('notice', "您的密码已修改成功，下次登录请使用新的密码。");

                return $this->refresh();
            }
        }

        return $this->render('change-password', [
            'model' => $model,
        ]);
    }

    /**
     * @return Member|null
     * @throws NotFoundHttpException
     */
    protected function findModel()
    {
        if (($model = Member::findOne(\Yii::$app->getUser()->getId())) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
