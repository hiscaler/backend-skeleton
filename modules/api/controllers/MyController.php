<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\AuthController;
use app\modules\api\models\Member;

class MyController extends AuthController
{

    /**
     * 个人资料
     *
     * @return array
     */
    public function actionProfile()
    {
        return [
            'profile' => Member::findOne(\Yii::$app->getUser()->getId()),
        ];
    }

}