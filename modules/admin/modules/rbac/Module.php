<?php

namespace app\modules\admin\modules\rbac;

use Yii;

/**
 * `rbac` 子模块
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class Module extends \app\modules\admin\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\modules\rbac\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        Yii::$app->setComponents([
            'formatter' => [
                'class' => 'app\modules\admin\modules\rbac\extensions\Formatter',
            ],
        ]);
    }

}
