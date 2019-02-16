<?php

namespace app\modules\api\modules\wechat\controllers;

use Yii;

/**
 * OAuth 授权
 * Class JssdkController
 *
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class JssdkController extends BaseController
{

    /**
     * JsSdk 配置值
     *
     * @param null $url
     * @param string $api
     * @param bool $debug
     * @param bool $beta
     * @return array|string
     */
    public function actionConfig($url = null, $api = '', $debug = false, $beta = true)
    {
        $validApi = ['checkJsApi', 'onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone'];
        $api = array_filter(explode(',', $api), function ($api) use ($validApi) {
            return $api && in_array($api, $validApi);
        });
        empty($api) && $api = ['checkJsApi'];

        $js = $this->_application->js;
        $url = $url ? urldecode($url) : Yii::$app->getRequest()->getHostInfo();
        $js->setUrl($url);

        return $js->config($api, $debug, $beta, false);
    }

}
