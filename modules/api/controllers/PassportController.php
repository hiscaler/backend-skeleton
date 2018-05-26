<?php

namespace app\modules\api\controllers;

use app\modules\api\models\Member;
use yii\base\InvalidArgumentException;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;

/**
 * 用户认证处理
 *
 * @package backend\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class PassportController extends Controller
{

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
        $password = $request->post('password');
        $token = $request->post('token');
        if ($token) {
            $checkBy = 'token';
            $member = Member::findIdentityByAccessToken($token);
        } else {
            $checkBy = 'username';
            $username = $request->post('username');
            if (empty($username) || empty($password)) {
                throw new InvalidArgumentException('无效的 username 或者 password 参数。');
            }
            $member = Member::findByUsername($username);
        }

        if (!$member) {
            throw new BadRequestHttpException($checkBy == 'token' ? '无效的 token' : '账号错误');
        }

        if ($checkBy == 'username' && !\Yii::$app->getSecurity()->validatePassword($password, $member['password'])) {
            throw new BadRequestHttpException('密码错误');
        }

        return [
            'id' => $member->id,
            'token' => $member->token,
        ];
    }

    /**
     * 刷新 Token
     *
     * @return array
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function actionRefreshToken()
    {
        $request = \Yii::$app->getRequest();
        $token = $request->post('token');
        if (empty($id) || empty($token)) {
            throw new InvalidArgumentException('无效的 token 参数。');
        }
        $user = AdminUser::findIdentityByAccessToken($token);
        if ($user === null) {
            throw new BadRequestHttpException('token 无效。');
        }

        if ($user::apiTokenIsValid($token)) {
            $newToken = $user->generateToken();
            \Yii::$app->getDb()->createCommand('{{%user}}', ['token' => $newToken], ['id' => $user->id])->execute();

            return [
                'id' => $user->id,
                'token' => $newToken,
            ];
        } else {
            throw new BadRequestHttpException('登录失败。');
        }
    }

}