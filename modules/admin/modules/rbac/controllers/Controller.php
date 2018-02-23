<?php

namespace app\modules\admin\modules\rbac\controllers;

use app\modules\admin\modules\rbac\helpers\RbacHelper;
use yii\base\Exception;

class Controller extends \yii\rest\Controller
{

    use RbacHelper;

    /** @var \yii\rbac\DbManager $auth */
    protected $auth;

    public function init()
    {
        parent::init();
        $this->auth = \Yii::$app->getAuthManager();
        if ($this->auth === null) {
            throw new Exception('Please setting authManager component in config file.');
        }
    }

}