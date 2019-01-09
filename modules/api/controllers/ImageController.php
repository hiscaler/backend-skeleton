<?php

namespace app\modules\api\controllers;

use Imagine\Image\ImageInterface;
use Yii;
use yii\base\InvalidArgumentException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use yii\web\Response;

/**
 * 图片处理
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class ImageController extends FileController
{

    protected $type = 'image';

    public function behaviors()
    {
        $behaviors = array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'uploading' => ['POST'],
                    'delete' => ['POST'],
                    'processing' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['uploading', 'delete', 'processing'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);

        return $behaviors;
    }

    /**
     * 图片处理
     *
     * @param string $action
     * @param $url 使用 urlencode 编码过的字符串
     * @param null $size 格式为宽度x高度，比如：60x90，如果只输入一个数字，则表示高度和宽度一致
     * @return \yii\web\Response|Response
     * @throws \yii\base\Exception
     */
    public function actionProcessing($url, $action = 'thumb', $size = null)
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

            if ($width > $imgWidth) {
                $width = $imgWidth;
                $height = $imgHeight;
                $sign = "0x0";
            } else {
                $sign = "{$width}x{$height}";
            }

            $afterFile = substr($beforeFile, 0, -(strlen($extensionName) + 1)) . "-$sign.$extensionName";
            if (file_exists($afterFile)) {
                $img = file_get_contents($afterFile);
            } else {
                $img = Image::thumbnail($beforeFile, $width, $height, ImageInterface::THUMBNAIL_OUTBOUND)
                    ->save($afterFile);
            }
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