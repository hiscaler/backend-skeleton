<?php

namespace app\modules\admin\widgets;

use app\models\Member;
use app\models\MemberCreditLog;
use app\models\Module;
use app\models\User;
use app\modules\admin\components\ApplicationHelper;
use Yii;
use yii\base\Widget;

/**
 * 全局管理控制面板
 *
 * @package app\modules\admin\widgets
 * @author hiscaler <hiscaler@gmail.com>
 */
class GlobalControlPanel extends Widget
{

    /**
     * @return array
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function getItems()
    {
        $user = \Yii::$app->getUser();
        $rbacConfig = ApplicationHelper::getConfigValue('rbac', []);
        $requireCheckAuth = isset($rbacConfig['debug']) && $rbacConfig['debug'] == false ? true : false;
        if ($requireCheckAuth) {
            $identity = $user->getIdentity();
            /* @var $identity Member */
            if ($user->identityClass != MemberCreditLog::class || $identity->type != Member::TYPE_ADMINISTRATOR) {
                $ignoreUsers = isset($rbacConfig['ignoreUsers']) ? $rbacConfig['ignoreUsers'] : [];
                if (!is_array($ignoreUsers)) {
                    $ignoreUsers = [];
                }
                if ($ignoreUsers) {
                    /* @var $identity User */
                    if (!$user->getIsGuest() && in_array($identity->getUsername(), $ignoreUsers)) {
                        $requireCheckAuth = false;
                    }
                }
            } else {
                // 验证类为 Member 并且类型是系统管理员则不需要走权限认证处理
                $requireCheckAuth = false;
            }
        }
        $items = [];
        $controllerId = Yii::$app->controller->id;
        $builtinModules = ApplicationHelper::getConfigValue('modules', []);

        foreach ($builtinModules as $group => $ms) {
            $rawItems = [];
            foreach ($ms as $key => $value) {
                if ((isset($value['forceEmbed']) && $value['forceEmbed'])) {
                    $url = $value['url'];
                    $r = $url[0];
                    $urlArray = explode('/', $r);
                    if ($requireCheckAuth) {
                        if ($urlArray[0] == 'admin') {
                            array_shift($urlArray);
                        }
                        if (!$user->can('admin-' . implode('.', $urlArray))) {
                            continue;
                        }
                    }

                    $urlControllerId = null;
                    foreach ($urlArray as $d) {
                        if (!empty($d)) {
                            $urlControllerId = $d;
                            break;
                        }
                    }
                    $url[0] = '/admin/' . $r;
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
            foreach ($installedModules as $module) {
                $t = ApplicationHelper::generateControlPanelModuleItem($module, $requireCheckAuth);
                $t && $items[] = $t;
            }
        }

        return $items;
    }

    /**
     * @return string
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function run()
    {
        return $this->render('ControlPanel', [
            'items' => $this->getItems(),
        ]);
    }

}
