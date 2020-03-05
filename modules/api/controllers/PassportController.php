<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\forms\ChangeMyPasswordForm;
use app\modules\api\forms\MemberLoginForm;
use app\modules\api\forms\MemberRegisterForm;
use app\modules\api\models\FrontendMember;
use app\modules\api\models\MemberProfile;
use Yii;
use yii\base\InvalidArgumentException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

/**
 * 用户认证处理
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class PassportController extends ActiveController
{

    public $modelClass = FrontendMember::class;

    public function actions()
    {
        return [];
    }

    /**
     * @var string token 参数名称
     */
    private $_token_param = 'access_token';

    public function behaviors()
    {
        $behaviors = array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'register' => ['POST'],
                    'login' => ['POST'],
                    'refresh-token' => ['POST'],
                    'change-password' => ['POST'],
                    '*' => ['GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['change-password', 'refresh-token'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['register', 'login', 'logout'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ]);
        if (in_array($this->action->id, ['login', 'logout'])) {
            unset($behaviors['authenticator']);
        }

        return $behaviors;
    }

    /**
     * 会员注册
     *
     * @return MemberRegisterForm|MemberProfile
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function actionRegister()
    {
        $payload = Yii::$app->getRequest()->getBodyParams();
        if (!isset($payload['profile'])) {
            $payload['profile'] = [];
        }
        $model = new MemberRegisterForm();
        $model->loadDefaultValues();

        $profileModel = new MemberProfile();
        $profileModel->loadDefaultValues();

        if ($model->load($payload, '')
            && $profileModel->load($payload, 'profile')
            && $model->validate()
            && $profileModel->validate()
        ) {
            $transaction = Yii::$app->getDb()->beginTransaction();
            try {
                $model->setPassword($model->password);
                $model->save();
                $model->saveProfile($profileModel);
                Yii::$app->getResponse()->setStatusCode(201);
                Yii::$app->getUser()->login($model, 0);
                $transaction->commit();
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        } elseif (!$model->hasErrors() && !$profileModel->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        if ($model->hasErrors()) {
            return $model;
        }
        if ($profileModel->hasErrors()) {
            return $profileModel;
        }

        return $model;
    }

    /**
     * 登录认证
     *
     * @return MemberLoginForm|\yii\web\IdentityInterface
     * @throws ServerErrorHttpException
     * @throws \Throwable
     */
    public function actionLogin()
    {
        $model = new MemberLoginForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->validate() && $model->login()) {
            return Yii::$app->getUser()->getIdentity();
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

    /**
     * 刷新 Token
     *
     * @return array
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     * @throws \yii\base\Exception
     */
    public function actionRefreshToken()
    {
        $request = Yii::$app->getRequest();
        $token = $request->getQueryParam($this->_token_param);
        if (empty($token)) {
            $headers = Yii::$app->getRequest()->getHeaders();
            $token = $headers->has($this->_token_param) ? $headers->get($this->_token_param) : null;
        }
        if (empty($token)) {
            throw new BadRequestHttpException("无效的 $this->_token_param 值。");
        }
        $class = $this->identityClass;
        $member = $class::findIdentityByAccessToken($token, AccessTokenAuth::class);
        if ($member === null) {
            throw new BadRequestHttpException("用户验证失败。");
        }

        $member->generateAccessToken();
        $newToken = $member->access_token;
        Yii::$app->getDb()->createCommand()->update('{{%member}}', ['access_token' => $newToken], ['id' => $member->id])->execute();

        return [
            'id' => $member->id,
            'access_token' => $newToken,
        ];
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
            $class = $this->identityClass;
            $member = $class::findIdentityByAccessToken($token);
            if ($member) {
                $model = new ChangeMyPasswordForm();
                $model->load($request->getBodyParams(), '');
                $member->setPassword($model->password);
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
     * 注销
     *
     * @return bool
     */
    public function actionLogout()
    {
        return Yii::$app->getUser()->logout();
    }

}