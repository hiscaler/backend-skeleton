<?php

namespace app\modules\admin\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

/**
 * 顶部菜单
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class MainMenu extends Widget
{

    public function getItems()
    {
        $controllerId = $this->view->context->id;
        $globalControllerIds = ['global'];
        $modules = ArrayHelper::getValue(Yii::$app->params, 'modules', []);
        $firstControllerId = null;
        foreach ($modules as $ms) {
            foreach ($ms as $item) {
                $urlControllerId = null;
                foreach (explode('/', $item['url'][0]) as $d) {
                    if (!empty($d)) {
                        $urlControllerId = $d;
                        break;
                    }
                }
                if ($urlControllerId) {
                    $globalControllerIds[] = $urlControllerId;
                    $firstControllerId === null && $firstControllerId = $urlControllerId;
                }
            }
        }

        $items = [
            [
                'label' => '首页',
                'url' => ['default/index'],
                'active' => $controllerId == 'default',
            ],
        ];
        if ($firstControllerId) {
            $items[] = [
                'label' => '全局管理',
                'url' => ["{$firstControllerId}/index"],
                'active' => in_array($controllerId, $globalControllerIds),
            ];
        }

        return $items;
    }

    public function run()
    {
        return $this->render('MainMenu', [
            'items' => $this->getItems(),
        ]);
    }

}
