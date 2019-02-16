<?php

namespace app\modules\api\modules\wechat\controllers;

/**
 * 客服
 * Class StaffController
 *
 * @property \EasyWeChat\Staff\Staff $service
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class StaffController extends BaseController
{

    public function init()
    {
        parent::init();
        $this->service = $this->_application->staff;
    }

    /**
     * 获取所有客服账号列表
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function actionIndex()
    {
        return $this->service->lists();
    }

    /**
     * 获取所有在线的客服账号列表
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function actionOnlines()
    {
        return $this->service->onlines();
    }

    /**
     * 添加客服帐号
     *
     * @param $account
     * @param $nickname
     * @return \EasyWeChat\Support\Collection
     */
    public function actionCreate($account, $nickname)
    {
        return $this->service->create($account, $nickname);
    }

    /**
     * 修改客服帐号
     *
     * @param $account
     * @param $nickname
     * @return \EasyWeChat\Support\Collection
     */
    public function actionUpdate($account, $nickname)
    {
        return $this->service->update($account, $nickname);
    }

    /**
     * 删除客服帐号
     *
     * @param $account
     */
    public function actionDelete($account)
    {
        $this->service->delete($account);
    }

    /**
     * 设置客服帐号的头像
     *
     * @param $account
     * @param $avatarPath
     * @return \EasyWeChat\Support\Collection
     */
    public function actionAvatar($account, $avatarPath)
    {
        return $this->service->avatar($account, $avatarPath);
    }

}
