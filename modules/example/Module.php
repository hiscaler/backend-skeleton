<?php

namespace app\modules\example;

/**
 * 测试模块
 */
class Module extends \app\modules\admin\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\example\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->modules = [
            'api' => [
                'class' => 'app\modules\example\modules\api\Module',
            ]
        ];
    }
}
