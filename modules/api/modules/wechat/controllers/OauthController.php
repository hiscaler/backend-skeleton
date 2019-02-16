<?php

namespace app\modules\api\modules\wechat\controllers;

use EasyWeChat\Foundation\Application;
use yadjet\helpers\UrlHelper;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;

/**
 * OAuth 授权
 * Class OauthController
 *
 * @property \Overtrue\Socialite\Providers\WeChatProvider $service
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class OauthController extends BaseController
{

    public function init()
    {
        parent::init();
        $this->service = $this->_application->oauth;
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
            $callbackUrl = $this->_config['oauth']['callback'];
            $url && $callbackUrl = UrlHelper::addQueryParam($callbackUrl, "url", $url);
            $type && $callbackUrl = UrlHelper::addQueryParam($callbackUrl, "type", $type);
            if (strcmp($callbackUrl, $this->_config['oauth']['callback']) !== 0) {
                $this->_config['oauth']['callback'] = $callbackUrl;
                $this->_application = new Application($this->_config);
            }
        }

        $this->service
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
     */
    public function actionCallback($url, $type = null)
    {
        $user = $this->service->scopes(['snsapi_userinfo'])->user();
        if ($user) {
            $required = ArrayHelper::getValue($this->_config, 'other.subscribe.required', false);
            if ($required && ($hintPageUrl = ArrayHelper::getValue($this->_config, 'other.subscribe.hintPageUrl'))) {
                $url = $hintPageUrl;
            } else {
                $url = UrlHelper::addQueryParam($url, 'openId', $user->getId());
            }

            return $this->redirect($url);
        } else {
            throw new BadRequestHttpException("Bad request.");
        }
    }

}
