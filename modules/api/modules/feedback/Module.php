<?php

namespace app\modules\api\modules\feedback;

use Yii;

/**
 * `feedback` module api definition class
 */
class Module extends \app\modules\api\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\api\modules\feedback\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        Yii::$app->setComponents([
            'formatter' => [
                'class' => 'app\modules\api\modules\feedback\extensions\Formatter',
            ],
        ]);
    }

}
