<?php

namespace app\modules\admin;

use app\helpers\Config;
use Yii;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $i18nTranslations = [
            '*' => [
                'class' => '\yii\i18n\PhpMessageSource',
                'basePath' => '@app/messages',
            ]
        ];
        $modules = \app\models\Module::map();
        foreach ($modules as $alias => $name) {
            $i18nTranslations["$alias*"] = [
                'class' => '\yii\i18n\PhpMessageSource',
                'basePath' => "@app/modules/admin/modules/$alias/messages",
            ];
        }
        $identityClass = Config::get('identity.class.backend', Yii::$app->getUser()->identityClass);
        Yii::$app->setComponents([
            'user' => [
                'class' => 'yii\web\User',
                'identityClass' => $identityClass,
                'identityCookie' => ['name' => '_identity_admin', 'httpOnly' => true],
                'idParam' => '__id_admin',
                'enableAutoLogin' => true,
                'loginUrl' => ['/admin/default/login'],
                'on afterLogin' => [$identityClass, 'afterLogin'],
            ],
            'formatter' => [
                'class' => 'app\modules\admin\extensions\Formatter',
            ],
            'assetManager' => [
                'class' => '\yii\web\AssetManager',
                'appendTimestamp' => true,
            ],
            'i18n' => [
                'class' => 'yii\i18n\I18N',
                'translations' => $i18nTranslations,
            ],
            'response' => [
                'class' => '\yii\web\Response',
                'formatters' => [
                    \yii\web\Response::FORMAT_JSON => [
                        'class' => '\yii\web\JsonResponseFormatter',
                        'prettyPrint' => YII_DEBUG,
                        'encodeOptions' => JSON_NUMERIC_CHECK + JSON_UNESCAPED_SLASHES + JSON_UNESCAPED_UNICODE,
                    ],
                ],
            ],
        ]);
        if (isset($modules['rbac'])) {
            Yii::$app->setComponents([
                'authManager' => [
                    'class' => 'yii\rbac\DbManager',
                ],
            ]);
        }
        if (isset($modules['queue']) && class_exists('\yii\queue\db\Queue')) {
            Yii::$app->setComponents([
                'queue' => [
                    'class' => \yii\queue\db\Queue::class,
                    'mutex' => \yii\mutex\MysqlMutex::class,
                    'channel' => 'default',
                    'as log' => \yii\queue\LogBehavior::class,
                ],
            ]);
        }
        Yii::$app->getErrorHandler()->errorAction = '/admin/default/error';

        // 载入安装的模块
        foreach ($modules as $alias => $name) {
            $this->setModule($alias, [
                'class' => 'app\\modules\\admin\\modules\\' . $alias . '\\Module',
                'layout' => '@app/modules/admin/views/layouts/main.php',
            ]);
        }
    }

}
