<?php

namespace app\modules\admin\widgets;

use Yii;
use yii\base\Widget;

/**
 * 用户登录日志
 */
class UserLoginLogs extends Widget
{

    public function getItems()
    {
        $items = [];
        $formatter = Yii::$app->getFormatter();
        $rawData = Yii::$app->getDb()->createCommand('SELECT [[t.login_ip]], [[t.client_informations]], [[t.login_at]] FROM {{%user_login_log}} t WHERE [[t.user_id]] = :userId ORDER BY [[t.login_at]] DESC')->bindValue(':userId', Yii::$app->getUser()->getId())->queryAll();
        foreach ($rawData as $data) {
            $items[$formatter->asDate($data['login_at'])][] = $data;
        }

        return $items;
    }

    public function run()
    {
        return $this->render('UserLoginLogs', [
            'items' => $this->getItems(),
        ]);
    }

}
