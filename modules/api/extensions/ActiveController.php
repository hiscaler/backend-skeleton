<?php

namespace app\modules\api\extensions;

use app\helpers\Config;
use app\modules\api\extensions\yii\filters\auth\AccessTokenAuth;
use app\modules\api\extensions\yii\rest\CreateAction;
use app\modules\api\extensions\yii\rest\ListAction;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\web\IdentityInterface;
use yii\web\Response;

/**
 * Class ActiveController
 *
 * @package app\modules\api\extensions
 * @author hiscaler <hiscaler@gmail.com>
 */
class ActiveController extends \yii\rest\ActiveController
{

    /**
     * 是否为调试模式
     *
     * @var boolean
     */
    protected $debug = false;

    /**
     *  数据缓存时间（秒）
     *
     * @var integer
     */
    protected $dbCacheTime = 3600;

    /**
     * @var array
     */
    public $serializer = [
        'class' => '\yii\rest\Serializer',
        'collectionEnvelope' => 'items',
        'expandParam' => 'expand',
    ];

    /**
     * @var IdentityInterface
     */
    protected $identityClass;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $dbCacheTime = Config::get('api.dbCacheDuration');
        $this->dbCacheTime = $dbCacheTime === null ? null : (int) $dbCacheTime;
        $this->debug = strtolower(trim(Yii::$app->getRequest()->get('debug'))) == 'y';
        $this->identityClass = Config::get('identityClass.frontend', Yii::$app->getUser()->identityClass);
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $behaviors = [
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

        $tokenParam = 'accessToken';
        $accessToken = Yii::$app->getRequest()->getQueryParam($tokenParam);
        if (empty($accessToken)) {
            $headers = \Yii::$app->getRequest()->getHeaders();
            $accessToken = $headers->has($tokenParam) ? $headers->get($tokenParam) : null;
        }
        if ($accessToken) {
            $behaviors = array_merge($behaviors, [
                'authenticator' => [
                    'class' => AccessTokenAuth::class,
                    'tokenParam' => $tokenParam
                ]
            ]);
        }

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        $actions['list'] = [
            'class' => ListAction::class,
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
        ];
        $actions['create'] = [
            'class' => CreateAction::class,
            'modelClass' => $this->modelClass,
            'checkAccess' => [$this, 'checkAccess'],
        ];

        return $actions;
    }

}