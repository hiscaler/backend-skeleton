<?php

namespace app\modules\api\controllers;

use app\modules\api\models\Member;

class MyController extends AuthController
{

    /**
     * 个人资料
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionProfile()
    {
        return [
            'profile' => Member::findOne(\Yii::$app->getUser()->getId()),
        ];
    }
}