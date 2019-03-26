<?php

namespace app\modules\api\modules\wechat\controllers;

/**
 * 客服
 * Class StaffController
 *
 * @property \EasyWeChat\Staff\Staff $wxService
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class StaffController extends Controller
{

    public function init()
    {
        parent::init();
        $this->wxService = $this->wxApplication->staff;
    }

    /**
     * 获取所有客服账号列表
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function actionIndex()
    {
        return $this->wxService->lists();
    }

    /**
     * 获取所有在线的客服账号列表
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function actionOnlines()
    {
        return $this->wxService->onlines();
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
        return $this->wxService->create($account, $nickname);
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
        return $this->wxService->update($account, $nickname);
    }

    /**
     * 删除客服帐号
     *
     * @param $account
     */
    public function actionDelete($account)
    {
        $this->wxService->delete($account);
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
        return $this->wxService->avatar($account, $avatarPath);
    }

}
