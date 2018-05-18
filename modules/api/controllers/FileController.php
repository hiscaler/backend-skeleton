<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use yadjet\helpers\StringHelper;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\UploadedFile;

class FileController extends BaseController
{

    public function actionUploading($key)
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