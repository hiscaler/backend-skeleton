<?php

namespace app\modules\api\extensions;

use app\modules\api\components\ApplicationHelper;
use Yii;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
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
    ];

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $dbCacheTime = ApplicationHelper::getConfigValue('api.db.cache.time');
        $this->dbCacheTime = $dbCacheTime === null ? null : (int) $dbCacheTime;
        $this->debug = strtolower(trim(Yii::$app->getRequest()->get('debug'))) == 'y';
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
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 86400, // One day
                ],
            ],
        ];
    }

}