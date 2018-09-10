<?php

namespace app\modules\admin\widgets;

use app\modules\admin\components\ApplicationHelper;
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

    /**
     * @return array
     * @throws \Throwable
     */
    public function getItems()
    {
        $controllerId = $this->view->context->id;
        $globalControllerIds = ['global'];
        $modules = ArrayHelper::getValue(Yii::$app->params, 'modules', []);
        $firstControllerId = null;
        $requireCheckAuth = ApplicationHelper::hasRequireCheckAuth();
        if ($requireCheckAuth) {
            $user = Yii::$app->getUser();
        } else {
            $user = null;
        }
        foreach ($modules as $ms) {
            foreach ($ms as $item) {
                if (!isset($item['forceEmbed']) || $item['forceEmbed'] == false) {
                    continue;
                }
                $urlRoute = explode('/', $item['url'][0]);
                if ($requireCheckAuth) {
                    $urlArray = $urlRoute;
                    if ($urlArray[0] == 'admin') {
                        array_shift($urlArray);
                    }
                    if (!$user->can('admin-' . implode('.', $urlArray))) {
                        continue;
                    }
                }

                $urlControllerId = null;
                foreach ($urlRoute as $r) {
                    if (!empty($r)) {
                        $urlControllerId = $r;
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
                'url' => ['/admin/default/index'],
                'active' => $controllerId == 'default',
            ],
        ];
        if ($firstControllerId) {
            $items[] = [
                'label' => '全局管理',
                'url' => ["/admin/{$firstControllerId}/index"],
                'active' => in_array($controllerId, $globalControllerIds),
            ];
        }

        return $items;
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function run()
    {
        return $this->render('MainMenu', [
            'items' => $this->getItems(),
        ]);
    }

}
