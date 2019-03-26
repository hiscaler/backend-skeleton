<?php

namespace app\modules\api\modules\wechat\controllers;

use Yii;

/**
 * JS SDK
 * Class JsController
 *
 * @property \EasyWeChat\Js\Js $wxService
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class JsController extends Controller
{

    public function init()
    {
        parent::init();
        $this->wxService = $this->wxApplication->js;
    }

    /**
     * JS SDK 配置值
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

        $url = $url ? urldecode($url) : Yii::$app->getRequest()->getHostInfo();
        $this->wxService->setUrl($url);

        return $this->wxService->config($api, $debug, $beta, false);
    }

}
