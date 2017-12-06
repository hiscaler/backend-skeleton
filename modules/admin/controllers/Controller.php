<?php

namespace app\modules\admin\controllers;

/**
 * Controller base class
 */
class Controller extends \yii\web\Controller
{

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            return true;
        }

        return false;
    }

}
