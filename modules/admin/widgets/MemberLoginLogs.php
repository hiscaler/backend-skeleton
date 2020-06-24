<?php

namespace app\modules\admin\widgets;

use Yii;
use yii\base\Widget;

/**
 * 会员登录日志
 *
 * @package app\modules\admin\widgets
 * @author hiscaler <hiscaler@gmail.com>
 */
class MemberLoginLogs extends Widget
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
        $rawData = Yii::$app->getDb()->createCommand('SELECT [[t.ip]], [[t.client_information]], [[t.login_at]] FROM {{%member_login_log}} t WHERE [[t.member_id]] = :memberId AND [[t.login_at]] >= :ts ORDER BY [[t.login_at]] DESC', [
            ':memberId' => Yii::$app->getUser()->getId(),
            ':ts' => strtotime('-2 weeks')
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
        return $this->render('MemberLoginLogs', [
            'items' => $this->getItems(),
        ]);
    }

}
