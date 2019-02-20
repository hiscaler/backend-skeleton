<?php

namespace app\modules\api\modules\wechat\controllers;

use app\modules\api\models\Constant;
use EasyWeChat\Foundation\Application;
use yadjet\helpers\UrlHelper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

/**
 * OAuth 授权
 * Class OauthController
 *
 * @property \Overtrue\Socialite\Providers\WeChatProvider $wxService
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class OauthController extends BaseController
{

    public function init()
    {
        parent::init();
        $this->wxService = $this->wxApplication->oauth;
    }

    /**
     * 拉起授权
     *
     * @param $url
     * @param null $type
     */
    public function actionRedirect($url, $type = null)
    {
        if ($url || $type) {
            $callbackUrl = $this->wxConfig['oauth']['callback'];
            $url && $callbackUrl = UrlHelper::addQueryParam($callbackUrl, "url", $url);
            $type && $callbackUrl = UrlHelper::addQueryParam($callbackUrl, "type", $type);
            if (strcmp($callbackUrl, $this->wxConfig['oauth']['callback']) !== 0) {
                $this->wxConfig['oauth']['callback'] = $callbackUrl;
                $this->wxApplication = new Application($this->wxConfig);
                $this->wxService = $this->wxApplication->oauth;
            }
        }

        $this->wxService
            ->scopes(['snsapi_userinfo'])
            ->redirect()
            ->send();
    }

    /**
     * 授权回调
     *
     * @param $url
     * @param null $type
     * @return \yii\web\Response
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     */
    public function actionCallback($url, $type = null)
    {
        $user = $this->wxService->scopes(['snsapi_userinfo'])->user();
        if ($user) {
            $openId = $user->getId();
            $isSubscribed = Yii::$app->getDb()->createCommand("SELECT [[subscribe]] FROM {{%wechat_member}} WHERE [[openid]] = :openid", [
                ':openid' => $openId
            ])->queryScalar();
            if ($isSubscribed == Constant::BOOLEAN_TRUE) {
                $url = UrlHelper::addQueryParam($url, 'openid', $openId);
            } else {
                if (
                    ArrayHelper::getValue($this->wxConfig, 'other.subscribe.required', false) &&
                    ($redirectUrl = ArrayHelper::getValue($this->wxConfig, 'other.subscribe.redirectUrl'))
                ) {
                    $url = $redirectUrl;
                }
            }

            return $this->redirect($url);
        } else {
            throw new BadRequestHttpException("Bad request.");
        }
    }

}
