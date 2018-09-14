<?php

namespace app\modules\api\controllers;

use app\modules\admin\components\ApplicationHelper;
use app\modules\api\extensions\BaseController;
use app\modules\api\models\Member;
use Yii;
use yii\base\InvalidArgumentException;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

/**
 * 用户认证处理
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class PassportController extends BaseController
{

    /**
     * @var string token 参数名称
     */
    private $_token_param = 'accessToken';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'login' => ['post'],
                    'refresh-token' => ['post'],
                ],
            ],
        ];
    }

    /**
     * 用户名和密码登录认证
     *
     * @return array
     * @throws BadRequestHttpException
     */
    public function actionLogin()
    {
        $request = \Yii::$app->getRequest();
        $token = $request->post($this->_token_param);
        if ($token) {
            $checkBy = 'accessToken';
            $member = Member::findIdentityByAccessToken($token);
        } else {
            $checkBy = 'username';
            $username = $request->post('username');
            $password = $request->post('password');
            if (empty($username) || empty($password)) {
                throw new InvalidArgumentException('无效的 username 或 password 参数。');
            }
            $member = Member::findByUsername($username);
        }

        if ($member === null) {
            throw new BadRequestHttpException($checkBy == 'accessToken' ? "无效的 $this->_token_param 值" : '账号错误');
        }

        if ($checkBy == 'username' && !\Yii::$app->getSecurity()->validatePassword($password, $member['password'])) {
            throw new BadRequestHttpException('密码错误');
        }

        return $member;
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
        $token = $request->post($this->_token_param);
        if (empty($token)) {
            throw new InvalidArgumentException("无效的 $this->_token_param 值。");
        }
        $member = Member::findIdentityByAccessToken($token);
        if ($member === null) {
            throw new BadRequestHttpException("用户验证失败。");
        }

        $t = explode('_', $token);
        $expiredTime = end($t);
        $accessTokenExpire = ApplicationHelper::getConfigValue('member.accessTokenExpire', 86400);
        $accessTokenExpire = (int) $accessTokenExpire ?: 86400;
        if (is_int($expiredTime) && ($expiredTime + $accessTokenExpire) <= time()) {
            $newToken = $member->generateAccessToken();
            \Yii::$app->getDb()->createCommand()->update('{{%user}}', ['access_token' => $newToken], ['id' => $member->id])->execute();

            return [
                'id' => $member->id,
                'token' => $newToken,
            ];
        } else {
            throw new BadRequestHttpException("$this->_token_param 已失效。");
        }
    }

}