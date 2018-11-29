<?php

namespace app\modules\api\controllers;

use app\modules\admin\components\ApplicationHelper;
use app\modules\api\extensions\BaseController;
use app\modules\api\extensions\yii\filters\auth\AccessTokenAuth;
use app\modules\api\forms\ChangeMyPasswordForm;
use app\modules\api\forms\MemberRegisterForm;
use app\modules\api\models\Member;
use Yii;
use yii\base\InvalidArgumentException;
use yii\filters\auth\QueryParamAuth;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;

/**
 * 用户认证处理
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class PassportController extends BaseController
{

    const LOGIN_BY_USERNAME = 'username';
    const LOGIN_BY_ACCESS_TOKEN = 'accessToken';

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
                    'register' => ['post'],
                    'login' => ['post'],
                    'refresh-token' => ['post'],
                    'change-password' => ['post'],
                ],
            ],
        ]);

        if (in_array($this->action->id, ['refresh-token', 'change-password', 'logout'])) {
            $token = \Yii::$app->getRequest()->get('accessToken');
            if (empty($token)) {
                $headers = \Yii::$app->getRequest()->getHeaders();
                $token = $headers->has('accessToken') ? $headers->get('accessToken') : null;
            }
            if (!empty($token)) {
                $class = AccessTokenAuth::class;
            } else {
                $class = QueryParamAuth::class;
            }

            $behaviors = array_merge($behaviors, [
                'authenticator' => [
                    'class' => $class,
                ]
            ]);
        }

        return $behaviors;
    }

    /**
     * 会员注册
     *
     * @return MemberRegisterForm
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRegister()
    {
        $model = new MemberRegisterForm();
        $model->loadDefaultValues();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute(['member/view', 'id' => $id], true));
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
            $loginBy = self::LOGIN_BY_USERNAME;
            $username = $request->post('username');
            $password = $request->post('password');
            if (empty($username) || empty($password)) {
                throw new InvalidArgumentException('无效的 username 或 password 参数。');
            }
            $member = Member::findByUsername($username);
        }

        if ($member === null) {
            throw new BadRequestHttpException($loginBy == self::LOGIN_BY_ACCESS_TOKEN ? "无效的 $this->_token_param 值" : '账号错误');
        }

        if ($loginBy == self::LOGIN_BY_USERNAME && isset($password) && !$member->validatePassword($password)) {
            throw new BadRequestHttpException('密码错误');
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
        $request = \Yii::$app->getRequest();
        $token = $request->get($this->_token_param);
        if ($token) {
            $member = Member::findIdentityByAccessToken($token);
            if ($member) {
                $password = $request->post('password');
                $payload = [
                    'oldPassword' => $request->post('oldPassword'),
                    'password' => $password,
                    'confirmPassword' => $request->post('confirmPassword'),
                ];
                $model = new ChangeMyPasswordForm();
                $model->load($payload, '');
                $member->setPassword($password);
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