<?php

namespace app\modules\admin\modules\rbac;

/**
 * `rbac` 子模块
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
        \Yii::$app->setComponents([
            'formatter' => [
                'class' => 'app\modules\admin\modules\rbac\extensions\Formatter',
            ],
            'i18n' => [
                'class' => 'yii\i18n\I18N',
                'translations' => [
                    'rbac' => [
                        'class' => '\yii\i18n\PhpMessageSource',
                        'basePath' => __DIR__ . '/messages',
                    ],
                ],
            ],
        ]);
    }
}
