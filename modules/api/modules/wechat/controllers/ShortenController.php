<?php

namespace app\modules\api\modules\wechat\controllers;

/**
 * 短网址服务
 *
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class ShortenController extends Controller
{

    public function init()
    {
        parent::init();
        $this->wxService = $this->wxApplication->url;
    }

    /**
     * 长链接转短链接
     *
     * @param $url
     * @return string
     */
    public function actionIndex($url)
    {
        return $this->wxService->shorten($url);
    }

}
