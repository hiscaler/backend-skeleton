<?php

namespace app\modules\api\modules\exam;

use Yii as YiiAlias;

/**
 * `exam` 模块接口
 *
 * @package app\modules\api\modules\exam
 * @author hiscaler <hiscaler@gmail.com>
 */
class Module extends \app\modules\api\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\api\modules\exam\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        YiiAlias::$app->setComponents([
            'formatter' => [
                'class' => 'app\modules\api\modules\exam\extensions\Formatter',
            ],
        ]);
    }

}
