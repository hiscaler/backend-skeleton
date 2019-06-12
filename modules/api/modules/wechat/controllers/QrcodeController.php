<?php

namespace app\modules\api\modules\wechat\controllers;

/**
 * 二维码
 *
 * @property \EasyWeChat\QRCode\QRCode $wxService
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class QrcodeController extends Controller
{

    public function init()
    {
        parent::init();
        $this->wxService = $this->wxApplication->qrcode;
    }

    /**
     * 创建临时二维码
     *
     * @param $sceneValue
     * @param null $expireSeconds
     * @return string
     */
    public function actionTemporary($sceneValue, $expireSeconds = null)
    {
        $result = $this->wxService->temporary($sceneValue, $expireSeconds);

        return $result->url;
    }

    /**
     * 创建永久二维码
     *
     * @param $sceneValue
     * @return string
     */
    public function actionForever($sceneValue)
    {
        $result = $this->wxService->forever($sceneValue);

        return $result->url;
    }

}
