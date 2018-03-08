<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\FileHelper;
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
     * @param $url 使用 urlencode 编码过的字符串
     * @param null $size
     * @return \yii\web\Response|Response
     * @throws \yii\base\Exception
     */
    public function actionIndex($action = 'thumb', $url, $size = null)
    {
        $actions = ['thumb'];
        if (!in_array($action, $actions)) {
            $action = 'thumb';
        }
        $url = urldecode($url);
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException('无效的 URL 参数。');
        }
        list($imgWidth, $imgHeight, $imgType) = getimagesize($url);
        if (!$imgWidth || !$imgHeight) {
            throw new InvalidArgumentException('无效的图片。');
        }

        switch ($imgType) {
            case IMAGETYPE_GIF:
                $extensionName = 'gif';
                break;

            case IMAGETYPE_JPEG:
                $extensionName = 'jpg';
                break;

            case IMAGETYPE_PNG:
                $extensionName = 'png';
                break;

            case IMAGETYPE_BMP:
                $extensionName = 'bmp';
                break;

            case IMAGETYPE_JPEG2000:
                $extensionName = 'jpeg';
                break;

            default:
                $extensionName = 'jpg';
                break;
        }
        $filename = md5($url) . ".$extensionName"; // 源文件名称
        $path = Yii::getAlias('@runtime/images/' . substr($filename, 0, 2) . DIRECTORY_SEPARATOR . substr($filename, 2, 2));
        if (!file_exists($path)) {
            FileHelper::createDirectory($path);
        }
        $beforeFile = $path . DIRECTORY_SEPARATOR . $filename;
        if (!file_exists($beforeFile)) {
            $img = file_get_contents($url);
            file_put_contents($beforeFile, $img);
        }

        $size = trim(strtolower($size));
        if ($size) {
            if (strpos($size, 'x')) {
                list($width, $height) = explode('x', $size);
                $height || $height = $width;
            } else {
                $width = $height = (int) $size;
            }
            $size = "{$width}x{$height}";
            $afterFile = substr($beforeFile, 0, -(strlen($extensionName) + 1)) . "-$size.$extensionName";
            if (!file_exists($afterFile)) {
                (new Imagine())
                    ->open($beforeFile)
                    ->thumbnail(new Box($width, $height))
                    ->save($afterFile);
            }
            $img = file_get_contents($afterFile);
        } else {
            // 返回原始图片
            $img = isset($img) ? $img : file_get_contents($beforeFile);
        }

        $response = \Yii::$app->getResponse();
        $response->headers->set('Content-Type', 'image/png');
        $response->format = Response::FORMAT_RAW;
        $response->content = $img;

        return $response;
    }

}