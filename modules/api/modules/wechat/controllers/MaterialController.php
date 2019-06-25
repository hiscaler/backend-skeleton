<?php

namespace app\modules\api\modules\wechat\controllers;

use app\helpers\Uploader;
use yii\base\DynamicModel;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * 永久素材管理
 * Class MaterialController
 *
 * @property \EasyWeChat\Material\Material $wxService
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class MaterialController extends Controller
{

    /**
     * 素材类型
     */
    const TYPE_IMAGE = 'image';
    const TYPE_VOICE = 'voice';
    const TYPE_VIDEO = 'video';
    const TYPE_THUMB = 'thumb';

    public function init()
    {
        parent::init();
        $this->wxService = $this->wxApplication->material;
    }

    /**
     * 文件上传
     *
     * @param string $type
     * @return Model
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    private function loadModel($type = self::TYPE_IMAGE)
    {
        $file = UploadedFile::getInstanceByName('file');
        $model = new DynamicModel([
            'path' => null,
            'file' => $file,
        ]);
        $validator = 'file';
        switch ($type) {
            case self::TYPE_IMAGE:
                $options = [
                    'extensions' => 'bmp, png, jpeg, jpeg, gif',
                    'minSize' => 1024,
                    'maxSize' => 1024 * 1024,
                ];
                $validator = 'image';
                break;

            case self::TYPE_VOICE:
                $options = [
                    'extensions' => 'mp3, wma, wav, amr',
                    'minSize' => 1024,
                    'maxSize' => 1024 * 2018,
                ];
                break;

            case self::TYPE_VIDEO:
                $options = [
                    'extensions' => 'mp4',
                    'minSize' => 1024,
                    'maxSize' => 1024 * 10240,
                ];
                break;

            default:
                $options = [
                    'extensions' => 'jpg',
                    'minSize' => 1024,
                    'maxSize' => 1024 * 64,
                ];
                $validator = 'image';
                break;
        }
        $model->addRule('file', $validator, $options);
        if ($model->validate()) {
            $uploader = new Uploader();
            $uploader->setFilename(null, $file->getExtension());
            $success = $file->saveAs($uploader->getPath());
            if ($success) {
                $model->path = $uploader->getPath();
            } else {
                $model->addError('file', '接口端文件存储失败。');
            }
        }

        return $model;
    }

    /**
     * 永久素材
     *
     * @return \EasyWeChat\Material\Material
     */
    public function actionIndex()
    {
        return $this->wxService;
    }

    /**
     * 上传图片
     *
     * @throws \yii\base\Exception
     */
    public function actionUploadImage()
    {
        $model = $this->loadModel(self::TYPE_IMAGE);
        if ($model->validate()) {
            return $this->wxService->uploadImage($model->path);
        } else {
            return $model;
        }
    }

}