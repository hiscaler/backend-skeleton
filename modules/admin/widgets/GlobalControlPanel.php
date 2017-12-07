<?php

namespace app\modules\admin\widgets;


use app\models\Module;
use Yii;
use yii\base\Widget;
use yii\helpers\ArrayHelper;

/**
 * 全局管理控制面板
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class GlobalControlPanel extends Widget
{

    public function getItems()
    {
        $items = [];
        $controller = Yii::$app->controller;
        $controllerId = $controller->id;
        $moduleId = $controller->module->id;
        $builtinModules = ArrayHelper::getValue(Yii::$app->params, 'modules', []);

        foreach ($builtinModules as $group => $ms) {
            $rawItems = [];
            foreach ($ms as $key => $value) {
                if ((isset($value['forceEmbed']) && $value['forceEmbed'])) {
                    $url = $value['url'];
                    $urlControllerId = null;
                    foreach (explode('/', $url[0]) as $d) {
                        if (!empty($d)) {
                            $urlControllerId = $d;
                            break;
                        }
                    }
                    $url[0] = '/admin/' . $url[0];
                    $activeConditions = isset($value['activeConditions']) ? in_array($controllerId, $value['activeConditions']) : $controllerId == $urlControllerId;
                    $rawItems[] = [
                        'label' => Yii::t('app', $value['label']),
                        'url' => $url,
                        'active' => $activeConditions,
                    ];
                }
            }

            if ($rawItems) {
                $items[$group] = [
                    'label' => Yii::t('app', Yii::t('app', $group)),
                ];
                $items[$group]['items'] = $rawItems;
            }
        }

        // 启用的模块
        $installedModules = Module::getInstalledModules();
        if ($installedModules) {
            $items['installedModules'] = [
                'label' => '模块管理',
                'items' => [],
            ];
            foreach ($installedModules as $module) {
                $items['installedModules']['items'][] = [
                    'label' => $module['name'],
                    'url' => ['/' . $module['alias'] . '/default/index'],
                    'active' => $moduleId == $module['alias'],
                ];
            }
        }


        return $items;
    }

    public function run()
    {
        return $this->render('ControlPanel', [
            'items' => $this->getItems(),
        ]);
    }

}
