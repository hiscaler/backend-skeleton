<?php

namespace app\modules\api\extensions;

use app\helpers\Config;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\Controller;
use yii\web\Response;

/**
 * Class BaseController
 *
 * @package app\modules\api\extensions
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseController extends Controller
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
    ];

    public function init()
    {
        parent::init();
        $dbCacheTime = Config::get('api.dbCacheDuration');
        $this->dbCacheTime = $dbCacheTime === null ? null : (int) $dbCacheTime;
        $this->debug = strtolower(trim(Yii::$app->getRequest()->get('debug'))) == 'y';
    }

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws \Throwable
     * @throws \yii\web\BadRequestHttpException
     * @throws \yii\web\UnauthorizedHttpException
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            return UtilsHelper::checkRbacAuth($this->module->getUniqueId(), $action);
        } else {
            return false;
        }
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