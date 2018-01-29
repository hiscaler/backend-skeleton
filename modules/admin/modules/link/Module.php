<?php

namespace app\modules\admin\modules\link;

/**
 * article module definition class
 */
class Module extends \yii\base\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\modules\article\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->modules = [
            'api' => [
                'class' => 'app\modules\admin\modules\article\modules\api\Module',
            ]
        ];
    }
}
