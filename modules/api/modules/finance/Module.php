<?php

namespace app\modules\api\modules\finance;

use Yii;

/**
 * `finance` 模块接口
 *
 * @package app\modules\api\modules\finance
 * @author hiscaler <hiscaler@gmail.com>
 */
class Module extends \app\modules\api\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\api\modules\finance\controllers';

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
