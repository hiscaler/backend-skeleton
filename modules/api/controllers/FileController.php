<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use RuntimeException;
use stdClass;
use yadjet\helpers\StringHelper;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * 文件处理
 * Class FileController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class FileController extends BaseController
{

    protected $type = 'file';

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!isset(Yii::$app->params['uploading'])) {
            throw new InvalidConfigException('无效的上传配置。');
        }
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function generateUniqueFilename()
    {
        return StringHelper::generateRandomString();
    }

    /**
     * 文件上传
     *
     * @param string $type
     * @param string $key
     * @return array
     * @throws InvalidConfigException
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function actionUploading($type = 'image', $key = 'file')
    {
        $config = Yii::$app->params['uploading'];
        $type = strtolower($type);
        if (!isset($config[$type])) {
            throw new InvalidConfigException("无效的 $type 上传配置。");
        }

        $request = Yii::$app->getRequest();
        $file = UploadedFile::getInstanceByName($key);
        $validator = null;
        if ($file) {
            $validator = $type;
        } else {
            $file = $request->post($key);
            !$file && $file = $request->get($key);
            if ($file) {
                $validator = 'string';
            }
        }
        if (empty($file)) {
            throw new BadRequestHttpException("key 参数值无效。");
        }

        $model = new DynamicModel([$key]);
        $model->$key = $file;
        switch ($validator) {
            case 'file':
            case 'image':
                $model->addRule($key, $validator, [
                    'skipOnEmpty' => false,
                    'enableClientValidation' => false,
                    'extensions' => $config[$type]['extensions'],
                    'minSize' => isset($config[$type]['minSize']) ? $config[$type]['minSize'] : 1024,
                    'maxSize' => isset($config[$type]['maxSize']) ? $config[$type]['maxSize'] : (1024 * 1024 * 200),
                ]);
                break;

            default:
                // Is base64 format
                $model->addRule($key, $validator, ['min' => 1]);
                break;
        }

        if ($model->validate()) {
            /* @var $file \yii\web\UploadedFile */
            $file = $model->$key;
            $path = $request->getBaseUrl() . '/' . trim($config['path'], '/') . '/' . date('Ymd');
            $absolutePath = FileHelper::normalizePath(Yii::getAlias('@webroot') . $path);
            !file_exists($absolutePath) && FileHelper::createDirectory($absolutePath);
            $originalName = null;
            $extensionName = null;
            if ($validator == 'string') {
                $t = explode(';', $file);
                $mimeType = substr($t[0], 5);
                $extensionName = FileHelper::getExtensionsByMimeType($mimeType);
                if ($extensionName) {
                    $extensionName = $extensionName[0];
                } else {
                    $extensionName = 'jpg';
                }

                $fileSize = 0;
            } else {
                $originalName = $file->name;
                $extensionName = $file->getExtension();
                $fileSize = $file->size;
                $mimeType = FileHelper::getMimeTypeByExtension($extensionName);
            }
            $filename = $this->generateUniqueFilename() . '.' . $extensionName;
            $absoluteSavePath = $absolutePath . DIRECTORY_SEPARATOR . $filename;
            if ($validator == 'string') {
                $success = file_put_contents($absoluteSavePath, base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $file)));
                $fileSize = filesize($absoluteSavePath);
            } else {
                $success = $file->saveAs($absoluteSavePath);
            }

            if ($success) {
                $path = "$path/$filename";
                $imgBinary = fread(fopen($absoluteSavePath, "r"), filesize($absoluteSavePath));
                $imageBase64 = 'data:image/' . $mimeType . ';base64,' . base64_encode($imgBinary);

                return [
                    'originalName' => $originalName,
                    'realName' => $filename,
                    'path' => $path,
                    'fullPath' => $request->getHostInfo() . $path,
                    'base64' => $imageBase64,
                    'size' => $fileSize,
                    'mimeType' => $mimeType,
                ];
            } else {
                Yii::$app->getResponse()->setStatusCode(400);

                return [
                    'message' => '文件上传失败。'
                ];
            }
        } else {
            Yii::$app->getResponse()->setStatusCode(400);

            return [
                'message' => $model->getFirstError('file')
            ];
        }
    }

    /**
     * 删除文件
     *
     * @todo 需要判断文件所有者权限
     * @param $url
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionDelete($url)
    {
        $url = parse_url($url);
        $file = $url['path'];
        $config = Yii::$app->params['uploading'];
        $dir = isset($config['dir']) ? $config['dir'] : "uploads";
        $path = Yii::getAlias("@webroot/$dir/" . trim($file, '\/'));
        if (file_exists($path)) {
            $success = @unlink($path);
            if ($success) {
                return new stdClass();
            } else {
                $error = error_get_last();
                if ($error !== null) {
                    $message = $error['message'];
                } else {
                    $message = '文件删除失败。';
                }
                throw new RuntimeException($message);
            }
        } else {
            throw new NotFoundHttpException("$url 文件不存在。");
        }
    }

}