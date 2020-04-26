<?php

namespace app\modules\api\extensions\yii\filters\auth;

use yii\filters\auth\AuthMethod;

/**
 * 增强 QueryParamAuth 认证，支持从 headers 中获取携带的 access-token 值
 *
 * @package app\modules\api\extensions\yii\filters\auth
 * @author hiscaler <hiscaler@gmail.com>
 */
class AccessTokenAuth extends AuthMethod
{

    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'access_token';

    /**
     * @param \yii\web\User $user
     * @param \yii\web\Request $request
     * @param \yii\web\Response $response
     * @return null|\yii\web\IdentityInterface
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->getQueryParam($this->tokenParam);
        $accessToken || $accessToken = $request->getHeaders()->get($this->tokenParam);
        if ($accessToken && is_string($accessToken)) {
            $identity = $user->loginByAccessToken($accessToken, get_class($this));
            if ($identity !== null) {
                return $identity;
            }
        }
        if ($accessToken !== null) {
            $this->handleFailure($response);
        }

        return null;
    }

}
