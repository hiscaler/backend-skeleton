<?php

namespace app\modules\admin\extensions;

use app\models\Lookup;
use app\modules\admin\components\ApplicationHelper;
use Yii;
use yii\helpers\Inflector;
use yii\web\Controller;
use yii\web\UnauthorizedHttpException;

/**
 * Class BaseController
 *
 * @package app\modules\admin\extensions
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseController extends Controller
{

    /**
     * @param $action
     * @return bool
     * @throws UnauthorizedHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            $user = \Yii::$app->getUser();
            if (!$user->getIsGuest() && ApplicationHelper::getConfigValue('disableRepeatingLogin', false) && $user->getIdentity()->last_login_session != session_id()) {
                \Yii::$app->getUser()->logout();
                $this->goHome();
            }
            Yii::$app->timeZone = Lookup::getValue('system.timezone', 'PRC');
            Yii::$app->language = Lookup::getValue('system.language', 'zh-CN');
            $formatter = Yii::$app->getFormatter();
            $formatter->datetimeFormat = Lookup::getValue('system.datetime-format', 'php:Y-m-d H:i:s');
            $formatter->dateFormat = Lookup::getValue('system.date-format', 'php:Y-m-d');
            $formatter->timeFormat = Lookup::getValue('system.time-format', 'php:H:i:s');
            $formatter->defaultTimeZone = $formatter->timeZone = Yii::$app->getTimeZone();

            $authManager = Yii::$app->getAuthManager();
            if ($authManager) {
                $rbacConfig = ApplicationHelper::getConfigValue('rbac', []);
                $requireCheckAuth = isset($rbacConfig['debug']) && $rbacConfig['debug'] == false ? true : false;
                if ($requireCheckAuth) {
                    $ignoreUsers = isset($rbacConfig['ignoreUsers']) ? $rbacConfig['ignoreUsers'] : [];
                    if (!is_array($ignoreUsers)) {
                        $ignoreUsers = [];
                    }
                    if ($ignoreUsers) {
                        if (!$user->getIsGuest() && in_array($user->getIdentity()->getUsername(), $ignoreUsers)) {
                            return true;
                        }
                    }

                    $defaultRoles = [
                        'admin-default.login',
                        'admin-default.logout',
                        'admin-default.error',
                        'admin-default.captcha',
                    ];
                    $authManager->defaultRoles = $defaultRoles;
                    $key = str_replace('/', '-', $this->module->getUniqueId());
                    $key && $key .= '-';
                    $key = $key . Inflector::camel2id(Yii::$app->controller->id) . '.' . Inflector::camel2id($action->id);
                    if (in_array($key, $defaultRoles) || $user->can($key)) {
                        return true;
                    } else {
                        throw new UnauthorizedHttpException('对不起，您没有操作该动作的权限。');
                    }
                }
            }

            return true;
        }

        return false;
    }
}