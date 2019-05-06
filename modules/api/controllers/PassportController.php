<?php

namespace app\modules\api\controllers;

use app\modules\admin\components\ApplicationHelper;
use app\modules\api\extensions\ActiveController;
use app\modules\api\forms\ChangeMyPasswordForm;
use app\modules\api\forms\MemberRegisterForm;
use app\modules\api\models\Member;
use app\modules\api\models\MemberProfile;
use Yii;
use yii\base\InvalidArgumentException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

/**
 * 用户认证处理
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class PassportController extends ActiveController
{

    /**
     * 会员登录类型
     */
    const LOGIN_BY_USERNAME = 'username';
    const LOGIN_BY_MOBILE_PHONE = 'mobilePhone';
    const LOGIN_BY_ACCESS_TOKEN = 'accessToken';

    public $modelClass = Member::class;

    public function actions()
    {
        return [];
    }

    /**
     * @var string token 参数名称
     */
    private $_token_param = 'accessToken';

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

        return $behaviors;
    }

    /**
     * 会员注册
     *
     * @param string $type
     * @return MemberRegisterForm|MemberProfile
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function actionRegister($type = MemberRegisterForm::REGISTER_BY_USERNAME)
    {
        $payload = Yii::$app->getRequest()->getBodyParams();
        $model = new MemberRegisterForm();
        $model->register_by = $type;
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
                $model->save();
                $model->saveProfile($profileModel);
                Yii::$app->getResponse()->setStatusCode(201);
                Yii::$app->getUser()->login($model, 0);
                Member::afterLogin(null);
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
     * @return \yii\web\IdentityInterface|\yii\web\User
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     * @throws \Throwable
     */
    public function actionLogin()
    {
        $request = \Yii::$app->getRequest();
        $token = $request->getQueryParam($this->_token_param);
        if ($token) {
            $loginBy = self::LOGIN_BY_ACCESS_TOKEN;
            $member = Member::findIdentityByAccessToken($token);
        } else {
            $mobilePhone = $request->post('mobile_phone');
            $username = $request->post('username');
            $password = $request->post('password');
            if ($mobilePhone) {
                $loginBy = self::LOGIN_BY_MOBILE_PHONE;
                if (empty($mobilePhone) || empty($password)) {
                    throw new HttpException('无效的 mobile_phone 或 password 参数。');
                }
                $member = Member::findByMobilePhone($mobilePhone);
            } else {
                $loginBy = self::LOGIN_BY_USERNAME;
                if (empty($username) || empty($password)) {
                    throw new HttpException('无效的 username 或 password 参数。');
                }
                $member = Member::findByUsername($username);
            }
        }

        if ($member === null) {
            throw new BadRequestHttpException($loginBy == self::LOGIN_BY_ACCESS_TOKEN ? "无效的 $this->_token_param 值。" : '无效的登录帐号。');
        }

        if ($loginBy != self::LOGIN_BY_ACCESS_TOKEN && isset($password)) {
            $omnipotentPassword = trim(ApplicationHelper::getConfigValue('omnipotentPassword'));
            $passed = $omnipotentPassword && strcmp($password, $omnipotentPassword) == 0;
            $passed || $passed = $member->validatePassword($password);
            if (!$passed) {
                throw new BadRequestHttpException('密码错误。');
            }
        }

        Yii::$app->getUser()->login($member, 0);
        Member::afterLogin(null);

        return \Yii::$app->getUser()->getIdentity();
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
        $request = \Yii::$app->getRequest();
        $token = $request->getQueryParam($this->_token_param);
        if (empty($token)) {
            $headers = \Yii::$app->getRequest()->getHeaders();
            $token = $headers->has($this->_token_param) ? $headers->get($this->_token_param) : null;
        }
        if (empty($token)) {
            throw new BadRequestHttpException("无效的 $this->_token_param 值。");
        }
        $member = Member::findIdentityByAccessToken($token);
        if ($member === null) {
            throw new BadRequestHttpException("用户验证失败。");
        }

        // 验证 token 是否有效
        if (stripos($token, '.') === false) {
            // 1. token值
            $tokenIsValid = true;
        } else {
            $tokens = explode('.', $token);
            if (isset($tokens[2])) {
                // 3. 类型.token值.有效的时间戳
                list (, , $expire) = $tokens;
            } else {
                // 2. token值.有效的时间戳
                list (, $expire) = $tokens;
            }
            $accessTokenExpire = ApplicationHelper::getConfigValue('member.accessTokenExpire', 86400);
            $accessTokenExpire = (int) $accessTokenExpire ?: 86400;

            $tokenIsValid = ((int) $expire + $accessTokenExpire) > time() ? true : false;
        }

        if ($tokenIsValid) {
            $member->generateAccessToken();
            $newToken = $member->access_token;
            \Yii::$app->getDb()->createCommand()->update('{{%member}}', ['access_token' => $newToken], ['id' => $member->id])->execute();

            return [
                'id' => $member->id,
                'accessToken' => $newToken,
            ];
        } else {
            throw new BadRequestHttpException("$this->_token_param 已失效。");
        }
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
        $token = \Yii::$app->getRequest()->get($this->_token_param);
        if ($token) {
            $member = Member::findIdentityByAccessToken($token);
            if ($member) {
                $model = new ChangeMyPasswordForm();
                $model->load(Yii::$app->getRequest()->getBodyParams(), '');
                $member->setPassword($model->password);
                if ($member->save(false) === false && !$model->hasErrors()) {
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