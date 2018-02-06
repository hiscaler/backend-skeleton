<?php

namespace app\modules\api\extensions;

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
     * @var array
     */
    public $serializer = [
        'class' => '\yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'text/javascript' => Response::FORMAT_JSONP,
                    'application/xml' => Response::FORMAT_XML,
                ],
            ],
            'corsFilter' => [
                'class' => Cors::className(),
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

    protected function send($data)
    {
        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $data,
        ]);
    }

}