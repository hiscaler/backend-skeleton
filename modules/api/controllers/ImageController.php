<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\StringHelper;
use yii\web\Response;

/**
 * 图片处理
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class ImageController extends BaseController
{

    /**
     * 图片处理
     *
     * @param string $action
     * @param $url
     * @param null $size
     * @return \yii\console\Response|Response
     */
    public function actionIndex($action = 'clip', $url, $size = null)
    {
        $actions = ['clip'];
        if (!in_array($action, $actions)) {
            $action = 'clip';
        }
        if (stripos($url, 'http') === false) {
            $url = StringHelper::base64UrlDecode($url);
        }
        if (stripos($url, 'http') === false) {
            throw new InvalidArgumentException('无效的 p 参数。');
        }
        list($imgWidth, $imgHeight) = getimagesize($url);
        if (!$imgWidth || !$imgHeight) {
            throw new InvalidArgumentException('无效的图片。');
        }
        $cacheKey = 'api-image-index-' . md5($action . $url . $size);
        $cache = Yii::$app->getCache();
        $img = $cache->get($cacheKey);
        if ($img === false) {
            $img = file_get_contents($url);
            $cache->set($cacheKey, $img, 0);
        }

        $response = \Yii::$app->getResponse();
        $response->headers->set('Content-Type', 'image/png');
        $response->format = Response::FORMAT_RAW;
        $response->content = $img;

        return $response;
    }

}