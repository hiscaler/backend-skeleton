<?php

namespace app\modules\admin\modules\example\modules\api;

/**
 * api module definition class
 */
class Module extends \app\modules\api\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\modules\example\modules\api\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
}
