<?php

namespace app\modules\api\modules\example;

/**
 * `example` 模块接口
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class Module extends \app\modules\api\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\api\modules\example\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
}
