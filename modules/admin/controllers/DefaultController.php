<?php

namespace app\modules\admin\controllers;

use app\models\Constant;
use app\models\User;
use app\models\Yad;
use app\modules\admin\forms\ChangeMyPasswordForm;
use app\modules\admin\forms\LoginForm;
use PDO;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * Site controller
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
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'captcha'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'profile', 'change-password', 'login-logs'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'transparent' => true,
                'offset' => 2,
                'padding' => 0,
                'height' => 32
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
        if (!Yii::$app->getUser()->isGuest) {
            return $this->redirect(['default/index']);
        }
        $this->layout = false;

        $model = new LoginForm();
        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->redirect(['/admin/default/index']);
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->getUser()->logout();
        // 清理 COOKIE 信息
        Yii::$app->getResponse()->getCookies()->remove('_tenant');

        return $this->goHome();
    }

    /**
     * 修改帐号资料
     *
     * @return mixed
     */
    public function actionProfile()
    {
        $model = $this->findCurrentUserModel();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'User profile save successed.'));

            return $this->redirect(['profile']);
        } else {
            return $this->render('profile', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Change current logined user password
     *
     * @return mixed
     */
    public function actionChangePassword()
    {
        $this->layout = 'my';
        $user = $this->findCurrentUserModel();
        $model = new ChangeMyPasswordForm();

        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            $user->setPassword($model->password);
            if ($user->save(false)) {
                Yii::$app->getSession()->setFlash('notice', "您的密码修改成功，请下次登录使用新的密码。");

                return $this->redirect(Url::previous());
            }
        }

        return $this->render('changePassword', [
            'user' => $user,
            'model' => $model,
        ]);
    }

    /**
     * 用户登录日志
     *
     * @return mixed
     */
    public function actionLoginLogs()
    {
        $this->layout = 'my';
        $loginLogs = [];
        $formatter = Yii::$app->formatter;
        $rawData = Yii::$app->getDb()->createCommand('SELECT [[t.login_ip]], [[t.client_informations]], [[t.login_at]] FROM {{%user_login_log}} t WHERE [[t.user_id]] = :userId ORDER BY [[t.login_at]] DESC')->bindValue(':userId', Yii::$app->getUser()->getId(), PDO::PARAM_INT)->queryAll();
        foreach ($rawData as $data) {
            $loginLogs[$formatter->asDate($data['login_at'])][] = $data;
        }

        return $this->render('loginLogs', [
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
