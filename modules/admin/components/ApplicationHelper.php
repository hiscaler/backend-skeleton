<?php

namespace app\modules\admin\components;

use app\helpers\App;
use app\helpers\Config;
use Yii;

/**
 * Class ApplicationHelper
 *
 * @package app\modules\admin\components
 * @author hiscaler <hiscaler@gmail.com>
 */
class ApplicationHelper
{

    /**
     * `app-model-Post` To `app\model\Post`
     *
     * @param string $id
     * @return string
     */
    public static function id2ClassName($id)
    {
        return str_replace('-', '\\', $id);
    }

    /**
     * 判断是否需要权限认证
     *
     * @return bool
     * @throws \Throwable
     */
    public static function hasRequireCheckAuth()
    {
        $has = App::rbacWorking();
        if ($has) {
            $rbacConfig = Config::get('rbac', []);
            $ignoreUsers = isset($rbacConfig['ignoreUsers']) ? $rbacConfig['ignoreUsers'] : [];
            if (!is_array($ignoreUsers)) {
                $ignoreUsers = [];
            }
            if ($ignoreUsers) {
                $user = Yii::$app->getUser();
                if (!$user->getIsGuest() && in_array($user->getIdentity()->getUsername(), $ignoreUsers)) {
                    $has = false;
                }
            }
        }

        return $has;
    }

    /**
     * 生成控制面板面板项目
     *
     * @param $module
     * @param null $requireCheckAuth
     * @return array
     * @throws \Throwable
     */
    public static function generateControlPanelModuleItem($module, $requireCheckAuth = null)
    {
        if ($requireCheckAuth === null) {
            $requireCheckAuth = self::hasRequireCheckAuth();
        }
        $request = Yii::$app->getRequest();
        $controller = Yii::$app->controller;
        $controllerId = $controller->id;
        $actionId = $controller->action->id;
        $moduleId = $controller->module->id;
        $moduleFullId = $controller->module->getUniqueId();
        $item = [
            'label' => $module['name'],
            'url' => ['/admin/' . $module['alias'] . '/default/index'],
            'template' => '<a id="control-panel-module-' . $module['alias'] . '" href="{url}">{label}</a>',
            'active' => $moduleId == $module['alias'],
        ];
        if (!empty($module['menus']) && ($moduleMenus = json_decode($module['menus'], true))) {
            foreach ($moduleMenus as $key => $menu) {
                if (isset($menu['url'][0])) {
                    $active = false;
                    if (isset($menu['active'])) {
                        $active = $moduleId == $module['alias'] || strpos($moduleFullId, $module['alias']) !== false;
                        if ($active) {
                            parse_str(trim($menu['active']), $conditions);
                            foreach ($conditions as $kk => $value) {
                                switch ($kk) {
                                    case 'moduleId':
                                        if ($moduleId != $value) {
                                            $active = false;
                                            break 2;
                                        }
                                        break;

                                    case 'controllerId':
                                        if ($controllerId != $value) {
                                            $active = false;
                                            break 2;
                                        }
                                        break;

                                    case 'actionId':
                                        if ($actionId != $value) {
                                            $active = false;
                                            break 2;
                                        }
                                        break;

                                    default:
                                        if ($value != $request->get($kk)) {
                                            $active = false;
                                            break 2;
                                        }
                                        break;
                                }
                            }
                        }
                    }

                    if (!$active) {
                        // /admin/moduleName/controllerId/actionId
                        $r = explode('/', $menu['url'][0]);
                        // $r[2] is module name, $r[3] is controller id, $r[4] is action id
                        if (isset($r[3], $r[4])) {
                            if ($r[2] == $module['alias'] && $r[3] == $controllerId && $r[4] == $actionId) {
                                $active = true;
                            }
                        }
                    }

                    $moduleMenus[$key]['active'] = $active;
                }
            }
            $item['items'] = $moduleMenus;
            $item['url'] = $moduleMenus[0]['url'];
        }

        if (!isset($item['items'][1])) {
            $item['items'] = [];
        }

        if ($requireCheckAuth) {
            if (isset($item['items']) && $item['items']) {
                // 有子菜单
                $urls = [];
                foreach ($item['items'] as $key => $moduleMenu) {
                    $urls[$key] = $moduleMenu['url'][0];
                }
                $hasChildrenMenu = true;
            } else {
                // 无子菜单
                $urls = [$item['url'][0]];
                $hasChildrenMenu = false;
            }
            $user = Yii::$app->getUser();
            foreach ($urls as $key => $url) {
                $urlArray = explode('/', trim($url, '/'));
                $permissionName = array_shift($urlArray) . '-' . array_shift($urlArray) . '-' . implode('.', $urlArray);
                if (!$user->can($permissionName)) {
                    if ($hasChildrenMenu) {
                        unset($item['items'][$key]);
                    } else {
                        $item = [];
                    }
                } elseif (!isset($rootUrl)) {
                    $rootUrl = $url;
                }
            }
            if (isset($rootUrl)) {
                $item['url'] = [$rootUrl];
            }
        }

        return $item;
    }

}
