<?php

namespace app\modules\admin\modules\slide;

/**
 * `slide` module definition class
 *
 * @package app\modules\admin\modules\slide
 * @author hiscaler <hiscaler@gmail.com>
 */
class Module extends \yii\base\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\modules\slide\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        \Yii::$app->setComponents([
            'formatter' => [
                'class' => 'app\modules\admin\modules\slide\extensions\Formatter',
            ],
        ]);
    }

}
