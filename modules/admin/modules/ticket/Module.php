<?php

namespace app\modules\admin\modules\ticket;

/**
 * `ticket` 子模块
 */
class Module extends \app\modules\admin\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\modules\ticket\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        \Yii::$app->setComponents([
            'formatter' => [
                'class' => 'app\modules\admin\modules\ticket\extensions\Formatter',
            ],
        ]);
    }

}
