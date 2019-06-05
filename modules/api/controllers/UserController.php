<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\forms\ChangeMyPasswordForm;
use app\modules\api\forms\UserRegisterForm;
use app\modules\api\models\User;
use InvalidArgumentException;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * api/user/ 接口
 * Class UserController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class UserController extends ActiveController
{

    /**
     * @var string token 参数名称
     */
    private $_token_param = 'accessToken';

    public $modelClass = User::class;

    public function behaviors()
    {
        $behaviors = array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                    'update' => ['PUT', 'PATCH'],
                    'delete' => ['POST'],
                    'change-password' => ['POST'],
                    '*' => ['GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'delete', 'change-password'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ]);

        return $behaviors;
    }

    /**
     * 用户注册
     *
     * @return UserRegisterForm
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public function actionCreate()
    {
        $model = new UserRegisterForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->validate() && $model->setPassword($model->password) && $model->save()) {
            Yii::$app->getResponse()->setStatusCode(201);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

    /**
     * 登录认证
     *
     * @return \yii\web\IdentityInterface|\yii\web\User
     * @throws BadRequestHttpException
     * @throws \Throwable
     */
    public function actionLogin()
    {
        $request = Yii::$app->getRequest();

        $username = $request->post('username');
        $password = $request->post('password');
        if (empty($username) || empty($password)) {
            throw new InvalidArgumentException('无效的 username 或 password 参数。');
        }
        $user = User::findByUsername($username);

        if ($user === null) {
            throw new BadRequestHttpException('帐号错误');
        }

        if (!$user->validatePassword($password)) {
            throw new BadRequestHttpException('密码错误');
        }

        Yii::$app->getUser()->login($user, 0);

        return Yii::$app->getUser()->getIdentity();
    }

    /**
     * 修改密码
     *
     * @return ChangeMyPasswordForm
     * @throws BadRequestHttpException
     * @throws \yii\base\Exception
     */
    public function actionChangePassword()
    {
        $request = Yii::$app->getRequest();
        $token = $request->get($this->_token_param);
        if ($token) {
            $member = User::findIdentityByAccessToken($token);
            if ($member) {
                $password = $request->post('password');
                $payload = [
                    'oldPassword' => $request->post('old_password'),
                    'password' => $password,
                    'confirmPassword' => $request->post('confirm_password'),
                ];
                $model = new ChangeMyPasswordForm();
                $model->load($payload, '');
                $member->setPassword($password);
                if ($model->validate() && $member->save(false) === false && !$model->hasErrors()) {
                    throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
                } else {
                    return $model;
                }
            } else {
                throw new BadRequestHttpException("用户验证失败。");
            }
        } else {
            throw new InvalidArgumentException("无效的 $this->_token_param 参数。");
        }
    }

    /**
     * @param $id
     * @return User
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        $model = User::findOne((int) $id);
        if ($model === null) {
            throw new NotFoundHttpException('Not found');
        }

        return $model;
    }

}