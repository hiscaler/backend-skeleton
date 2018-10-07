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
            'loginUrl' => ['site/login']
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.example.com',
                'username' => '',
                'password' => '',
                'port' => '587',
                'encryption' => 'ssl',
            ],
        ],
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
    ];
}

return $config;
