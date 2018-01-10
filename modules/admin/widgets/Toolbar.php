<?php

namespace app\modules\admin\widgets;

use app\models\Constant;
use app\models\User;
use app\models\Yad;
use Yii;
use yii\base\Widget;

class Toolbar extends Widget
{

    public function getItems()
    {
        $items = [];
        $user = Yii::$app->getUser();
        if (!$user->isGuest) {
            $items[] = [
                'label' => $user->getIdentity()->username . (($user->getIdentity()->role == User::ROLE_ADMINISTRATOR) ? ' [ M ]' : ''),
                'url' => ['/admin/default/profile'],
            ];

            $items[] = [
                'label' => Yii::t('app', 'Logout'),
                'url' => ['/admin/default/logout'],
                'template' => '<a id="logout" href="{url}">{label}</a>'
            ];
        }

        return $items;
    }

    public function run()
    {
        return $this->render('Toolbar', [
            'items' => $this->getItems(),
        ]);
    }

}
