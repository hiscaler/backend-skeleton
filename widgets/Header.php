<?php

namespace app\widgets;

use Yii;

/**
 * 网站头部
 */
class Header extends \yii\base\Widget
{

    public function run()
    {
        return $this->render('Header', [
                'controllerId' => $this->view->context->id,
        ]);
    }

}
