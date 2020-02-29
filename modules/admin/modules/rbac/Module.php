<?php

namespace app\modules\admin\modules\rbac;

use Yii;
use yii\web\Response;

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
            'response' => [
                'class' => 'yii\web\Response',
                'on beforeSend' => function ($event) {
                    $response = $event->sender;
                    if ($response->format == Response::FORMAT_HTML) {
                        return $response;
                    } else {
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
                    }
                },
            ],
        ]);
    }

}
