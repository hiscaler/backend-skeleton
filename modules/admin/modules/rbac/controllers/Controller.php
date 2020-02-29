<?php

namespace app\modules\admin\modules\rbac\controllers;

use app\modules\admin\modules\rbac\helpers\RbacHelper;
use Yii;
use yii\base\Exception;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\web\Response;

/**
 * Class Controller
 * 基类
 *
 * @package app\modules\admin\modules\rbac\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class Controller extends \yii\rest\Controller
{

    use RbacHelper;

    /** @var \yii\rbac\DbManager $auth */
    protected $auth;

    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();
        $this->auth = \Yii::$app->getAuthManager();
        if ($this->auth === null) {
            throw new Exception('Please setting authManager component in config file.');
        }
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
    }

    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'text/javascript' => Response::FORMAT_JSONP,
                    'application/xml' => Response::FORMAT_XML,
                ],
            ],
            'corsFilter' => [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => false,
                    'Access-Control-Max-Age' => 86400, // One day
                ],
            ],
        ];
    }

}