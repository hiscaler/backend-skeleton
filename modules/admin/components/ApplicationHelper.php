<?php

namespace app\modules\admin\components;

use Yii;
use yii\helpers\ArrayHelper;

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
        $user = Yii::$app->getUser();
        $rbacConfig = ApplicationHelper::getConfigValue('rbac', []);
        $has = isset($rbacConfig['debug']) && $rbacConfig['debug'] == false ? true : false;
        if ($has) {
            $ignoreUsers = isset($rbacConfig['ignoreUsers']) ? $rbacConfig['ignoreUsers'] : [];
            if (!is_array($ignoreUsers)) {
                $ignoreUsers = [];
            }
            if ($ignoreUsers) {
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

        if ($requireCheckAuth) {
            if (isset($item['items'])) {
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

    /**
     * 是否存在指定的配置项
     *
     * @param $key
     * @return bool
     */
    public static function hasConfigKey($key)
    {
        $has = false;
        $params = Yii::$app->params;
        if (array_key_exists($key, $params)) {
            $has = true;
        } elseif (strpos($key, '.') !== false) {
            $levels = explode('.', $key);
            $n = count($levels) - 1;
            foreach ($levels as $i => $level) {
                if (array_key_exists($level, $params)) {
                    $params = $params[$level];
                    if ($i == $n) {
                        $has = true;
                    }
                } else {
                    break;
                }
            }
        }

        return $has;
    }

    /**
     * 获取配置参数值
     *
     * @param $key
     * @param null $defaultValue
     * @return mixed|null
     */
    public static function getConfigValue($key, $defaultValue = null)
    {
        $params = Yii::$app->params;
        if (isset($params[$key])) {
            $value = $params[$key];
        } elseif (strpos($key, '.') !== false) {
            $value = ArrayHelper::getValue($params, $key, $defaultValue);
        } else {
            $value = $defaultValue;
        }

        return $value;
    }

}
