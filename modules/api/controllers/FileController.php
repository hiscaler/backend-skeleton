<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use yadjet\helpers\StringHelper;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
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
     */
    public function actionUploading($type = 'image', $key = 'file')
    {
        if (!isset(Yii::$app->params['uploading'])) {
            throw new InvalidConfigException('无效的上传配置。');
        }

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

    public function _actionUploading($key)
    {
        $file = UploadedFile::getInstanceByName($key);
        if ($file === null) {
            throw new BadRequestHttpException('获取文件失败。');
        }
        $maxSize = 1024 * 1024 * 8; // 8MB

        if ($file->size > $maxSize) {
            $responseBody = [
                'success' => false,
                'error' => [
                    'message' => '文件大小超过 ' . Yii::$app->getFormatter()->asShortSize($maxSize) . '，禁止上传。'
                ]
            ];
        } else {
            $imageSavePath = '/uploads/' . date('Ymd') . '/';
            if (!is_dir(Yii::getAlias('@webroot' . $imageSavePath))) {
                FileHelper::createDirectory(Yii::getAlias('@webroot' . $imageSavePath));
            }
            $extension = $file->getExtension();
            $fileType = $file->type;
            if (!$extension) {
                $extension = 'jpeg';
            }
            $imgPath = $imageSavePath . 'tmp-' . StringHelper::generateRandomString() . '.' . $extension;
            if (!$file->saveAs(\Yii::getAlias('@webroot') . $imgPath)) {
                $imgPath = null;
            }
            if ($imgPath) {
//                Image::thumbnail(Yii::getAlias('@webroot') . $imgPath, 300, 450)
//                    ->save(str_replace(".{$extension}", "_thumb.{$extension}", Yii::getAlias('@webroot') . $imgPath), ['quality' => 60]);
//                $db = Yii::$app->getDb();
//                $columns = [
//                    'type' => $type,
//                    'dining_request_id' => $id,
//                    'filename' => $file->getBaseName(),
//                    'path' => $imgPath,
//                    'description' => $file->getBaseName(),
//                    'created_at' => time(),
//                    'created_by' => Yii::$app->getUser()->getId()
//                ];
//                $db->createCommand()->insert('{{%dining_photo}}', $columns)->execute();
                $filename = \Yii::getAlias('@webroot') . $imgPath;
                $imgbinary = fread(fopen($filename, "r"), filesize($filename));
                $photo = 'data:image/' . $fileType . ';base64,' . base64_encode($imgbinary);

                return [
                    'photoPath' => $imgPath,
                    'photo' => $photo,
                    'deleteUrl' => Url::toRoute(['delete-photo', 'id' => 12]),
                    'uploadCompleted' => true,
                ];
            } else {
                throw new \Exception('文件保存失败。');
            }
        }

        return $responseBody;

//        return new Response([
//            'format' => Response::FORMAT_JSON,
//            'data' => $responseBody
//        ]);
    }

}