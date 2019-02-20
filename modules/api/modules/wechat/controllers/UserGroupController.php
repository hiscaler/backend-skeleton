<?php

namespace app\modules\api\modules\wechat\controllers;

/**
 * 用户组
 * Class UserGroupController
 *
 * @property \EasyWeChat\User\Group $wxService
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class UserGroupController extends BaseController
{

    public function init()
    {
        parent::init();
        $this->wxService = $this->wxApplication->user_group;
    }

    /**
     * 获取用户列表
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function actionIndex()
    {
        return $this->wxService->lists();
    }

    /**
     * 添加分组
     *
     * @param $name
     * @return \EasyWeChat\Support\Collection
     */
    public function actionCreate($name)
    {
        return $this->wxService->create($name);
    }

    /**
     * 更新分组
     *
     * @param $groupId
     * @param $name
     * @return \EasyWeChat\Support\Collection
     */
    public function actionUpdate($groupId, $name)
    {
        return $this->wxService->update($groupId, $name);
    }

    /**
     * 删除分组
     *
     * @param $groupId
     * @return \EasyWeChat\Support\Collection
     */
    public function actionDelete($groupId)
    {
        return $this->wxService->delete($groupId);
    }

    /**
     * 移动用户到指定分组
     *
     * @param $openId
     * @param $groupId
     * @return \EasyWeChat\Support\Collection
     */
    public function actionMoveUser($openId, $groupId)
    {
        if (strpos($openId, ',') === false) {
            return $this->wxService->moveUser($openId, $groupId);
        } else {
            return $this->wxService->moveUsers(explode(',', $openId), $groupId);
        }
    }

}
