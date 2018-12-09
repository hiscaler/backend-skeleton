<?php

namespace app\modules\admin\controllers;

use app\modules\admin\forms\LoginForm;
use yadjet\http\Http;
use Yii;
use yii\filters\AccessControl;

/**
 * Default controller
 * Class DefaultController
 *
 * @package app\modules\admin\controllers
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
                        'actions' => ['login', 'error', 'captcha'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
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

    /**
     * 首页
     *
     * @rbacIgnore true
     * @return string
     */
    public function actionIndex()
    {
        $gitComments = [];
        $http = new Http('https://api.github.com');
        $http->httpHeaders = ['User-Agent: hiscaler'];
        $rawGitComments = $http->get('/repos/hiscaler/backend-skeleton/commits');

        if ($rawGitComments !== false && is_array($rawGitComments)) {
            foreach ($rawGitComments as $comment) {
                if (stripos($comment['commit']['message'], '...') !== false) {
                    continue;
                }
                $gitComments[] = [
                    'author' => $comment['commit']['author']['name'],
                    'date' => $comment['commit']['author']['date'],
                    'message' => $comment['commit']['message']
                ];
            }
        }

        return $this->render('index', [
            'gitComments' => $gitComments,
        ]);
    }

    /**
     * 登录
     *
     * @rbacIgnore true
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->getUser()->getIsGuest()) {
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

    /**
     * 用户注销
     *
     * @rbacIgnore true
     * @return void
     */
    public function actionLogout()
    {
        Yii::$app->getUser()->logout();

        $this->redirect(['default/login']);
    }

}
