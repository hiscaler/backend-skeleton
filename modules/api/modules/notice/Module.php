<?php

namespace app\modules\api\modules\notice;

use Yii;

/**
 * `notice` module api definition class
 */
class Module extends \app\modules\api\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\api\modules\notice\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        Yii::$app->setComponents([
            'formatter' => [
                'class' => 'app\modules\api\modules\finance\extensions\Formatter',
            ],
        ]);
    }

}
