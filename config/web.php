<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'zh-CN',
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
            'layout' => 'main.php',
        ],
        'api' => [
            'class' => 'app\modules\api\Module',
        ],
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '^*H((*_U_(YH&^R^&%EDFVGBHJ)K(',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Member',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
            'on afterLogin' => ['app\models\Member', 'afterLogin'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => require(__DIR__ . '/mail.php'),
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => true,
            'rules' => [
                'admin/<controller>' => 'admin/<controller>/index',
                'admin/<controller>/<id:\d+>' => 'admin/<controller>/view',
                'admin/<controller>/update/<id:\d+>' => 'admin/<controller>/update',
                'admin/<controller>/delete/<id:\d+>' => 'admin/<controller>/delete',
                'admin/<controller>/<action>' => 'admin/<controller>/<action>',
                'admin/<module>/<controller>' => 'admin/<module>/<controller>/index',
                'admin/<module>/<controller>/<id:\d+>' => 'admin/<module>/<controller>/view',
                'admin/<module>/<controller>/update/<id:\d+>' => 'admin/<module>/<controller>/update',
                'admin/<module>/<controller>/delete/<id:\d+>' => 'admin/<module>/<controller>/delete',
            ],
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => '\yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                ],
            ],
        ],
        'formatter' => [
            'class' => 'app\extensions\Formatter',
        ],
        'assetManager' => [
            'appendTimestamp' => true,
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null, // do not publish the bundle
                    'js' => [
                        '/js/jquery.min.js',
                    ]
                ],
            ],
        ],
    ],
    'params' => $params,
];

if (!YII_DEBUG) {
    $config['components']['db']['enableSchemaCache'] = true;
}

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
        'generators' => [
            'model' => [
                'class' => 'yii\gii\generators\model\Generator',
                'templates' => [
                    'bs' => '@app/giiTemplates/model/default',
                ]
            ],
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator',
                'templates' => [
                    'bs' => '@app/giiTemplates/crud/default',
                ]
            ],
            'controller' => [
                'class' => 'yii\gii\generators\controller\Generator',
                'templates' => [
                    'bs' => '@app/giiTemplates/controller/default',
                ]
            ],
            'form' => [
                'class' => 'yii\gii\generators\form\Generator',
                'templates' => [
                    'bs' => '@app/giiTemplates/form/default',
                ]
            ],
            'module' => [
                'class' => 'yii\gii\generators\module\Generator',
                'templates' => [
                    'bs' => '@app/giiTemplates/module/default',
                ]
            ],
            'extension' => [
                'class' => 'yii\gii\generators\extension\Generator',
                'templates' => [
                    'bs' => '@app/giiTemplates/extension/default',
                ]
            ],
        ],
    ];
}

return $config;
