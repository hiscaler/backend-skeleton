<?php

namespace app\modules\admin\widgets;

use Yii;
use yii\base\Widget;

/**
 * 用户登录日志
 */
class UserLoginLogs extends Widget
{

    /**
     * @return array
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function getItems()
    {
        $items = [];
        $formatter = Yii::$app->getFormatter();
        $rawData = Yii::$app->getDb()->createCommand('SELECT [[t.login_ip]], [[t.client_information]], [[t.login_at]] FROM {{%user_login_log}} t WHERE [[t.user_id]] = :userId AND [[t.login_at]] >= :ts ORDER BY [[t.login_at]] DESC', [
            ':userId' => Yii::$app->getUser()->getId(),
            ':ts' => strtotime('-2 months')
        ])->queryAll();
        $days = 7;
        foreach ($rawData as $data) {
            $key = $formatter->asDate($data['login_at']);
            if (!isset($items[$key])) {
                $days--;
                if ($days < 0) {
                    break;
                }
            }

            $items[$key][] = $data;
        }

        return $items;
    }

    /**
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function run()
    {
        return $this->render('UserLoginLogs', [
            'items' => $this->getItems(),
        ]);
    }

}
