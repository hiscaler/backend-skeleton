<?php

namespace app\modules\api\controllers;

use app\models\Member;
use Yii;

class MyController extends AuthController
{

    /**
     * 我的个人资料
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionIdentity()
    {
        $db = Yii::$app->getDb();
        $userId = Yii::$app->getUser()->getId();
        $totalMoney = $db->createCommand('SELECT SUM([[money]]) FROM {{%donate_log}} WHERE [[member_id]] = :memberId', [':memberId' => $userId])->queryScalar();
        if ($totalMoney) {
            $totalHelps = $db->createCommand('SELECT COUNT(DISTINCT([[pianolude_id]])) FROM {{%donate_log}} WHERE [[member_id]] = :memberId', [':memberId' => $userId])->queryScalar();
        } else {
            $totalHelps = 0;
        }

        return [
            'identity' => Member::findOne($userId),
            'totalMoney' => $totalMoney ?: 0,
            'totalHelps' => $totalHelps ?: 0,
        ];
    }
}