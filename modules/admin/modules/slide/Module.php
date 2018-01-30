<?php

namespace app\modules\admin\modules\article;

/**
 * slide module definition class
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
        $this->modules = [
            'api' => [
                'class' => 'app\modules\admin\modules\slide\modules\api\Module',
            ]
        ];
    }
}
