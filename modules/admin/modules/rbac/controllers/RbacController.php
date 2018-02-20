<?php

namespace app\modules\admin\modules\rbac\controllers;

use Yii;
use yii\helpers\Inflector;

class RbacController extends Controller
{

    public function beforeAction($action)
    {
        $defaultRoles = Yii::$app->getAuthManager()->defaultRoles;
        $action = Yii::$app->id . '@' . Inflector::camelize(Yii::$app->controller->id) . Inflector::camelize($action->id);
        if (Yii::$app->getUser()->can($action) || in_array($action, $defaultRoles)) {
            return true;
        } else {
            throw new UnauthorizedHttpException('对不起，您没有操作该项目的权限。');
        }
    }

}