<?php

namespace app\modules\admin\modules\wechat;

/**
 * wechat module definition class
 */
class Module extends \yii\base\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\modules\wechat\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        \Yii::$app->setComponents([
            'formatter' => [
                'class' => 'app\modules\admin\modules\wechat\extensions\Formatter',
            ],
        ]);
    }
}
