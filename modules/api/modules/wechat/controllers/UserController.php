<?php

namespace app\modules\api\modules\wechat\controllers;

/**
 * 用户
 * Class UserController
 *
 * @property \EasyWeChat\User\User $wxService
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class UserController extends BaseController
{

    public function init()
    {
        parent::init();
        $this->wxService = $this->wxApplication->user;
    }

    /**
     * 获取用户列表
     *
     * @param null $nextOpenId
     * @return \EasyWeChat\Support\Collection
     */
    public function actionIndex($nextOpenId = null)
    {
        return $this->wxService->lists($nextOpenId);
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
            return $this->wxService->get($openId);
        } else {
            return $this->wxService->batchGet(explode(',', $openId));
        }
    }

}
