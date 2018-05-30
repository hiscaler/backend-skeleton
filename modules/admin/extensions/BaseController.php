<?php

namespace app\modules\admin\extensions;

use app\models\Lookup;
use Yii;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;

class BaseController extends Controller
{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            Yii::$app->timeZone = Lookup::getValue('system.timezone', 'PRC');
            Yii::$app->language = Lookup::getValue('system.language', 'zh-CN');
            $formatter = Yii::$app->getFormatter();
            $formatter->datetimeFormat = Lookup::getValue('system.datetime-format', 'php:Y-m-d H:i:s');
            $formatter->dateFormat = Lookup::getValue('system.date-format', 'php:Y-m-d');
            $formatter->timeFormat = Lookup::getValue('system.time-format', 'php:H:i:s');

            $authManager = Yii::$app->getAuthManager();
            if ($authManager) {
                $rbacConfig = isset(Yii::$app->params['rbac']) ? Yii::$app->params['rbac'] : [];
                $requireCheckAuth = isset($rbacConfig['debug']) && $rbacConfig['debug'] == false ? true : false;
                if ($requireCheckAuth) {
                    $ignoreUsers = isset($rbacConfig['ignoreUsers']) ? $rbacConfig['ignoreUsers'] : [];
                    if (!is_array($ignoreUsers)) {
                        $ignoreUsers = [];
                    }
                    if ($ignoreUsers) {
                        $user = \Yii::$app->getUser();
                        if (!$user->getIsGuest() && in_array($user->getIdentity()->getUsername(), $ignoreUsers)) {
                            $requireCheckAuth = false;
                        }
                    }
                }

                if ($requireCheckAuth) {
                    $defaultRoles = Yii::$app->getCache()->get('admin.rbac.default.roles');
                    $defaultRoles || $defaultRoles = [];
                    $defaultRoles = array_merge($defaultRoles, [
                        'admin-default.login',
                        'admin-default.logout',
                        'admin-default.error',
                        'admin-default.captcha',
                    ]);
                    $authManager->defaultRoles = $defaultRoles;
                    $key = str_replace('/', '-', $this->module->getUniqueId());
                    if ($key) {
                        $key .= '-';
                    }
                    $key = $key . Inflector::camel2id(Yii::$app->controller->id) . '.' . Inflector::camel2id($action->id);
                    if (in_array($key, $defaultRoles) || Yii::$app->getUser()->can($key)) {
                        return true;
                    } else {
                        throw new UnauthorizedHttpException('对不起，您没有操作该项目的权限。');
                    }
                }
            }

            return true;
        }

        return false;
    }
}