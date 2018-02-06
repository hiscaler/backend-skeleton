<?php

namespace app\modules\admin\modules\feedback;

/**
 * `feedback` 子模块
 */
class Module extends \app\modules\admin\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\modules\feedback\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        \Yii::$app->setComponents([
            'formatter' => [
                'class' => 'app\modules\admin\modules\feedback\extensions\Formatter',
            ],
        ]);
    }
}
