<?php

namespace app\modules\admin\widgets;

use yii\base\Widget;

/**
 * 帐号控制面板
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class AccountControlPanel extends Widget
{

    public function getItems()
    {
        $items = [
            [
                'label' => '帐号信息',
                'url' => ['account/index'],
            ],
            [
                'label' => '修改密码',
                'url' => ['account/change-password'],
            ],
            [
                'label' => '登录日志',
                'url' => ['account/login-logs'],
            ],
        ];

        return $items;
    }

    public function run()
    {
        return $this->render('ControlPanel', [
            'items' => $this->getItems(),
        ]);
    }

}
