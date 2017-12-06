<?php

namespace app\modules\admin\widgets;

use app\models\Tenant;
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
        $controller = $this->view->context;
        $controllerId = $controller->id;
        $modules = ArrayHelper::getValue(Yii::$app->params, 'modules', []);

        $tenantModules = Tenant::modules();
        foreach ($modules as $group => $ms) {
            $rawItems = [];
            foreach ($ms as $key => $value) {
                if ((isset($value['forceEmbed']) && $value['forceEmbed']) || in_array($key, $tenantModules)) {
                    $url = $value['url'];
                    $urlControllerId = null;
                    foreach (explode('/', $url[0]) as $d) {
                        if (!empty($d)) {
                            $urlControllerId = $d;
                            break;
                        }
                    }
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

        return $items;
    }

    public function run()
    {
        return $this->render('ControlPanel', [
            'items' => $this->getItems(),
        ]);
    }

}
