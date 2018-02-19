<?php

namespace app\modules\admin\controllers;

use app\models\User;
use app\modules\admin\forms\ChangeMyPasswordForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * 帐号管理
 * Class AccountController
 *
 * @package app\modules\admin\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class AccountController extends Controller
{

    public $layout = 'account';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'change-password', 'login-logs'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 帐号资料
     *
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $model = $this->findCurrentUserModel();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('notice', Yii::t('app', 'User profile save successed.'));

            $this->refresh();
        } else {
            return $this->render('index', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 修改密码
     *
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException
     */
    public function actionChangePassword()
    {
        $user = $this->findCurrentUserModel();
        $model = new ChangeMyPasswordForm();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $user->setPassword($model->password);
            if ($user->save(false)) {
                Yii::$app->getSession()->setFlash('notice', "您的密码修改成功，请下次登录使用新的密码。");

                return $this->refresh();
            }
        }

        return $this->render('change-password', [
            'user' => $user,
            'model' => $model,
        ]);
    }

    /**
     * 用户登录日志
     *
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionLoginLogs()
    {
        $loginLogs = [];
        $formatter = Yii::$app->formatter;
        $rawData = Yii::$app->getDb()->createCommand('SELECT [[t.login_ip]], [[t.client_information]], [[t.login_at]] FROM {{%user_login_log}} t WHERE [[t.user_id]] = :userId ORDER BY [[t.login_at]] DESC', [':userId' => Yii::$app->getUser()->getId()])->queryAll();
        foreach ($rawData as $data) {
            $data['login_ip'] = long2ip($data['login_ip']);
            $loginLogs[$formatter->asDate($data['login_at'])][] = $data;
        }

        return $this->render('login-logs', [
            'loginLogs' => $loginLogs
        ]);
    }

    public function findCurrentUserModel()
    {
        if (($model = User::findOne(Yii::$app->getUser()->getId())) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
