<?php

namespace app\controllers;

use app\forms\ForgetPasswordForm;
use app\forms\ResetPasswordForm;
use app\forms\SigninForm;
use app\forms\SignupForm;
use app\models\Constant;
use app\models\Meta;
use app\modules\admin\components\DynamicMetaModel;
use Yii;
use yii\base\Security;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Cookie;

class SiteController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
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
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->redirect(['items/index']);
    }

    /**
     * 用户注册
     *
     * @return mixed
     */
    public function actionSignup($next = null)
    {
        $this->layout = 'base';
        $next = strtolower($next);
        if ($next == 'message') {
            return $this->render('signup', [
                'next' => $next,
            ]);
        } else {
            $model = new SignupForm();
            $metaItems = Meta::getItems($model, $this->tenantId);
            $dynamicModel = DynamicMetaModel::make($metaItems);

            if ($model->load(Yii::$app->getRequest()->post()) && $dynamicModel->load(Yii::$app->getRequest()->post()) && $model->validate()) {
                $model->password_hash = (new Security())->generatePasswordHash($model->password);
                if ($model->save()) {
                    Meta::saveValues($model, $dynamicModel, $this->tenantId); // 保存 Meta 数据

                    return $this->redirect(['signup', 'next' => 'message']);
                }
            }

            return $this->render('signup', [
                'model' => $model,
                'metaItems' => $metaItems,
                'dynamicModel' => $dynamicModel,
            ]);
        }
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionSignin()
    {
        $this->layout = 'base';
        if (!Yii::$app->getUser()->isGuest) {
            return $this->goHome();
        }

        $model = new SigninForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('signin', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->getUser()->logout();

        return $this->goHome();
    }

    /**
     * 忘记密码
     *
     * @return mixed
     */
    public function actionForgetPassword()
    {
        $model = new ForgetPasswordForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $body = $this->render("@app/mail/layouts/find-password.php", [
                'content' => "{$model->username}，您提交了密码找回申请，请点击该链接地址完成后续认证。",
                'url' => '<a href="' . Url::toRoute(['site/reset-password', 'token' => $model['token']], true) . '">点击修改密码</a>',
            ]);
            $send = Yii::$app->getMailer()->compose('layouts/html', [
                'content' => $body
            ])->setFrom(Yii::$app->params['fromMailAddress'])->setTo($model->email)->setSubject('测试邮件')->setHtmlBody($body)->send();
            if ($send) {
                Yii::$app->getSession()->setFlash('notice', '邮件发送成功，请注意查收。');
            } else {
                Yii::$app->getSession()->setFlash('notice', '邮件发送失败，请联系系统管理员。');
            }
        }

        return $this->render('forget-password', [
            'model' => $model,
        ]);
    }

    /**
     * 重置密码
     *
     * @param string $token
     * @return mixed
     */
    public function actionResetPassword($token)
    {
        $model = new ResetPasswordForm();
        $model->token = $token;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->_user->setPassword($model->password);
            Yii::$app->getDb()->createCommand()->update('{{%user}}', ['password_hash' => $model->_user->password_hash, 'password_reset_token' => null], ['id' => $model->_user->id])->execute();
            Yii::$app->getSession()->setFlash('notice', "您的密码修改成功，请下次登录使用新的密码。");
        }

        return $this->render('reset-password', [
            'model' => $model,
        ]);
    }

    /**
     * 切换语言
     *
     * @param string $lang
     */
    public function actionLanguage($lang = null)
    {
        $lang = strtolower($lang);
        $languages = ['zh-cn' => 1, 'en-us' => 2, 'es' => 3];
        if (!isset($languages[$lang])) {
            $lang = 'zh-cn';
        }

        $tenant = Yii::$app->getDb()->createCommand('SELECT [[id]], [[language]], [[timezone]], [[date_format]], [[time_format]], [[datetime_format]] FROM {{%tenant}} WHERE [[id]] = :id AND [[enabled]] = :enabled')->bindValues([
            ':id' => $languages[$lang],
            ':enabled' => Constant::BOOLEAN_TRUE
        ])->queryOne();
        if ($tenant) {
            $cookie = new Cookie(['name' => '_site', 'httpOnly' => true]);
            $cookie->value = [
                'id' => $tenant['id'],
                'language' => $tenant['language'],
                'timezone' => $tenant['timezone'],
                'dateFormat' => $tenant['date_format'],
                'timeFormat' => $tenant['time_format'],
                'datetimeFormat' => $tenant['datetime_format'],
            ];
            $cookie->expire = time() + 86400 * 365;
            Yii::$app->getResponse()->getCookies()->add($cookie);
        }

        return $this->redirect(Yii::$app->getRequest()->referrer);
    }

}
