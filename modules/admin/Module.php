<?php

namespace app\modules\admin;

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
        \Yii::$app->setComponents([
            'user' => [
                'class' => 'yii\web\User',
                'identityClass' => 'app\models\User',
                'identityCookie' => ['name' => '_identity_admin', 'httpOnly' => true],
                'idParam' => '__id_admin',
                'enableAutoLogin' => true,
                'loginUrl' => ['/admin/default/login']
            ],
            'formatter' => [
                'class' => 'app\modules\admin\extensions\Formatter',
            ],
            'assetManager' => [
                'class' => '\yii\web\AssetManager',
                'appendTimestamp' => true,
                'bundles' => [
                    'yii\web\JqueryAsset' => [
                        'sourcePath' => null, // do not publish the bundle
                        'js' => [
                            '/admin/js/jquery.min.js',
                        ]
                    ],
                    'yii\grid\GridViewAsset' => [
                        'js' => ['/admin/js/yii.gridView.js'],
                    ],
                ],
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
        \Yii::$app->errorHandler->errorAction = 'admin/default/error';
    }

}
