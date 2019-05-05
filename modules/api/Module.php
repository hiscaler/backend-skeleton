<?php

namespace app\modules\api;

use app\modules\api\components\ApplicationHelper;
use app\modules\api\models\Member;
use Yii;
use yii\web\Response;

/**
 * api module definition class
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class Module extends \yii\base\Module
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\api\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        \Yii::$app->setComponents([
            'user' => [
                'class' => 'yii\web\User',
                'identityClass' => ApplicationHelper::getConfigValue('api.user.identityClass', Member::class),
                'identityCookie' => ['name' => '_identity_api', 'httpOnly' => true],
                'idParam' => '__id_api',
                'enableAutoLogin' => true,
                'enableSession' => false,
                'loginUrl' => null,
            ],
            'request' => [
                'class' => 'yii\web\Request',
                'cookieValidationKey' => '#$%^&*()PFVK:"PVR&F:PO()_H)HN',
                'parsers' => [
                    'application/json' => 'yii\web\JsonParser',
                    'multipart/form-data' => 'yii\web\MultipartFormDataParser',
                ],
            ],
            'response' => [
                'class' => 'yii\web\Response',
                'formatters' => [
                    Response::FORMAT_JSON => [
                        'class' => 'yii\web\JsonResponseFormatter',
                        'encodeOptions' => JSON_NUMERIC_CHECK + JSON_UNESCAPED_UNICODE,
                        'prettyPrint' => YII_DEBUG,
                    ],
                ],
                'on beforeSend' => function ($event) {
                    $response = $event->sender;
                    if ($response->data !== null && $response->isSuccessful) {
                        $response->data = [
                            'success' => true,
                            'data' => $response->data,
                        ];
                        $response->statusCode = 200;
                    } else {
                        if ($response->format != Response::FORMAT_RAW) {
                            $response->data = [
                                'success' => false,
                                'error' => $response->data,
                            ];
                        }
                    }
                },
            ],
        ]);
        $response = Yii::$app->getResponse();
        $response->format && $response->format = Response::FORMAT_JSON;

        $modules = \app\models\Module::map();
        if (isset($modules['queue']) && class_exists('\yii\queue\db\Queue')) {
            \Yii::$app->setComponents([
                'queue' => [
                    'class' => \yii\queue\db\Queue::class,
                    'mutex' => \yii\mutex\MysqlMutex::class,
                    'channel' => 'default',
                    'as log' => \yii\queue\LogBehavior::class,
                ],
            ]);
        }

        // 载入已经安装的模块
        foreach ($modules as $alias => $name) {
            $this->setModule($alias, [
                'class' => 'app\\modules\\api\\modules\\' . $alias . '\\Module',
            ]);
        }
    }

}
