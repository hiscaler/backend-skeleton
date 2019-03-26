<?php

namespace app\modules\api\modules\wechat\controllers;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * 菜单
 * Class MenuController
 *
 * @property \EasyWeChat\Menu\Menu $wxService
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class MenuController extends Controller
{

    public function init()
    {
        parent::init();
        $this->wxService = $this->wxApplication->menu;
    }

    /**
     * 读取（查询）已设置菜单
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function actionIndex()
    {
        return $this->wxService->all();
    }

    /**
     * 添加菜单
     *
     * @return \EasyWeChat\Support\Collection
     */
    public function actionCreate()
    {
        $buttons = ArrayHelper::getValue($this->wxConfig, 'other.menu.buttons');
        $matchRule = ArrayHelper::getValue($this->wxConfig, 'other.menu.matchRule');
        $menu = $this->wxService->add($buttons, $matchRule);
        $response = Yii::$app->getResponse();
        $response->setStatusCode(201);

        return $menu;
    }

    /**
     * 删除菜单
     *
     * @param null $menuId
     */
    public function actionDelete($menuId = null)
    {
        $this->wxService->destroy($menuId);
    }

    /**
     * 测试个性化菜单
     *
     * @param $userId
     * @return \EasyWeChat\Support\Collection
     */
    public function actionTest($userId)
    {
        return $this->wxService->test($userId);
    }

}
