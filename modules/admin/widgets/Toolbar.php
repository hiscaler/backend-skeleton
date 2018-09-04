<?php

namespace app\modules\admin\widgets;

use Yii;
use yii\base\Widget;

class Toolbar extends Widget
{

    /**
     * @return array
     * @throws \Throwable
     */
    public function getItems()
    {
        $items = [];
        $user = Yii::$app->getUser();
        if (!$user->getIsGuest()) {
            $identity = $user->getIdentity();
            $items = [
                [
                    'label' => $identity->getUsername() . ($identity->getRole() ? " [ {$identity->getRole()} ] " : ''),
                    'url' => ['/admin/account/index'],
                ],
                [
                    'label' => Yii::t('app', 'Logout'),
                    'url' => ['/admin/default/logout'],
                    'template' => '<a id="logout" href="{url}">{label}</a>'
                ]
            ];
        }
        $items[] = [
            'label' => Yii::t('app', 'Help'),
            'url' => ['/admin/help/index'],
            'template' => '<a target="_blank" href="{url}">{label}</a>'
        ];
        $items[] = [
            'label' => Yii::t('app', 'Frontend Page'),
            'url' => Yii::$app->getRequest()->getHostInfo(),
            'template' => '<a target="_blank" href="{url}">{label}</a>'
        ];

        return $items;
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function run()
    {
        return $this->render('Toolbar', [
            'items' => $this->getItems(),
        ]);
    }

}
