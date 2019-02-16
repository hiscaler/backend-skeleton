<?php

namespace app\modules\api\modules\wechat\controllers;

/**
 * 用户组
 * Class UserGroupController
 *
 * @property \EasyWeChat\User\Group $service
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class UserGroupController extends BaseController
{

    public function init()
    {
        parent::init();
        $this->service = $this->_application->user_group;
    }

    /**
     * 获取用户列表
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function actionIndex()
    {
        return $this->service->lists();
    }

    /**
     * 添加分组
     *
     * @param $name
     * @return \EasyWeChat\Support\Collection
     */
    public function actionCreate($name)
    {
        return $this->service->create($name);
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
        return $this->service->update($groupId, $name);
    }

    /**
     * 删除分组
     *
     * @param $groupId
     * @return \EasyWeChat\Support\Collection
     */
    public function actionDelete($groupId)
    {
        return $this->service->delete($groupId);
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
            return $this->service->moveUser($openId, $groupId);
        } else {
            return $this->service->moveUsers(explode(',', $openId), $groupId);
        }
    }

}
