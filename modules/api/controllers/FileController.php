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
        return '';
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

        $model = new DynamicModel([$key]);
        $model->$key = UploadedFile::getInstanceByName($key);
        $model->addRule($key, $type, [
            'skipOnEmpty' => false,
            'enableClientValidation' => false,
            'extensions' => $config[$type]['extensions'],
            'minSize' => isset($config[$type]['minSize']) ? $config[$type]['minSize'] : 1024,
            'maxSize' => isset($config[$type]['maxSize']) ? $config[$type]['maxSize'] : (1024 * 1024 * 200),
        ]);
        if ($model->validate()) {
            /* @var $file \yii\web\UploadedFile */
            $file = $model->$key;
            $path = $config['path'];
            $absolutePath = Yii::getAlias('@web') . $path . DIRECTORY_SEPARATOR . date("Ymd");
            !file_exists($absolutePath) && FileHelper::createDirectory($absolutePath);
            $fileNameHash = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz1234567890'), 0, 5);
            $filename = sprintf('%s%s.%s', $fileNameHash, date('YmdHis'), $file->getExtension());
            $success = $file->saveAs($absolutePath . DIRECTORY_SEPARATOR . $filename);
            if ($success) {
                $path = "/$path/$filename";

                return [
                    'originalName' => $file->name,
                    'realName' => $filename,
                    'path' => $path,
                    'fullPath' => Yii::$app->getRequest()->getHostInfo() . $path,
                    'size' => $file->size,
                    'type' => $file->getExtension(),
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