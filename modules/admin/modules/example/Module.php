<?php

namespace app\modules\admin\modules\example;

/**
 * 测试模块
 */
class Module extends \app\modules\admin\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\modules\example\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->modules = [
            'api' => [
                'class' => 'app\modules\admin\modules\example\modules\api\Module',
            ]
        ];
    }
}
