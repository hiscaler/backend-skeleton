<?php

namespace app\modules\api;

use app\helpers\Config;
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
        $identityClass = Config::get('api.user.identityClass', Member::class);
        Yii::$app->setComponents([
            'user' => [
                'class' => 'yii\web\User',
                'identityClass' => $identityClass,
                'identityCookie' => ['name' => '_identity_api', 'httpOnly' => true],
                'idParam' => '__id_api',
                'enableAutoLogin' => true,
                'enableSession' => false,
                'loginUrl' => null,
                'on afterLogin' => [$identityClass, 'afterLogin'],
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

        $modules = $this->getDevelopmentModules();
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

        // 载入已经安装的模块
        foreach ($modules as $alias => $name) {
            $this->setModule($alias, [
                'class' => 'app\\modules\\api\\modules\\' . $alias . '\\Module',
            ]);
        }
    }

    /**
     * 获取开发模块
     *
     * @return array
     */
    public function getDevelopmentModules()
    {
        $key = '__api.development.modules';
        $cache = Yii::$app->getCache();
        $modules = $cache->get($key);
        if ($modules === false) {
            $modules = \app\models\Module::map();
            $cache->set($key, $modules, 3600);
        }

        return $modules;
    }

}
