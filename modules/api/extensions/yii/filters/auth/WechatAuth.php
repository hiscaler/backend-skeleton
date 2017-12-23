<?php

namespace app\modules\api\extensions\yii\filters\auth;

use yii\filters\auth\AuthMethod;

/**
 * WechatAuth 用于微信用户有效性验证
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class WechatAuth extends AuthMethod
{

    /**
     * @var string the parameter name for passing the access token
     */
    public $tokenParam = 'accessToken';

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        $accessToken = $request->get($this->tokenParam);
        if (is_string($accessToken)) {
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
