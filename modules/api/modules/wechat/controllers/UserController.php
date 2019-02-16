<?php

namespace app\modules\api\modules\wechat\controllers;

/**
 * 用户
 * Class UserController
 *
 * @property \EasyWeChat\User\User $service
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class UserController extends BaseController
{

    public function init()
    {
        parent::init();
        $this->service = $this->_application->user;
    }

    /**
     * 获取用户列表
     *
     * @param null $nextOpenId
     * @return \EasyWeChat\Support\Collection
     */
    public function actionIndex($nextOpenId = null)
    {
        return $this->service->lists($nextOpenId);
    }

    /**
     * 获取用户信息
     *
     * @param $openId
     * @return \EasyWeChat\Support\Collection
     */
    public function actionView($openId)
    {
        if (strpos($openId, ',') === false) {
            return $this->service->get($openId);
        } else {
            return $this->service->batchGet(explode(',', $openId));
        }
    }

}
