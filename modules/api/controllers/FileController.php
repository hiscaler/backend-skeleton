<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use RuntimeException;
use stdClass;
use yadjet\helpers\ImageHelper;
use yadjet\helpers\StringHelper;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
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

    const TYPE_IMAGE = 'image';
    const TYPE_FILE = 'file';

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

    public function behaviors()
    {
        $behaviors = array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'uploading' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['uploading', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ]);

        return $behaviors;
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
     * @param string $key
     * @param bool $single
     * @return array|mixed|DynamicModel
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     * @throws \yii\base\Exception
     * @throws \Exception
     */
    public function actionUploading($key = 'file', $single = true)
    {
        $config = Yii::$app->params['uploading'];
        if (!isset($config[$this->type])) {
            throw new InvalidConfigException("无效的 $this->type 上传配置。");
        }

        $request = Yii::$app->getRequest();
        $files = UploadedFile::getInstancesByName($key);
        $validator = null;
        if ($files) {
            $validator = $this->type;
        } else {
            $files = $request->post($key);
            !$files && $files = $request->get($key);
            if ($files) {
                $validator = 'string';
            }
            if ($files && !is_array($files)) {
                $files = [$files];
            }
        }
        if (empty($files)) {
            throw new BadRequestHttpException("未检测到 $key 参数数据。");
        }

        $models = [];
        foreach ($files as $i => $file) {
            $attr = $key . '_' . $i;
            $model = new DynamicModel([$attr]);
            $model->$attr = $file;
            switch ($validator) {
                case 'file':
                case 'image':
                    $model->addRule($attr, $validator, [
                        'skipOnEmpty' => false,
                        'enableClientValidation' => false,
                        'extensions' => $config[$this->type]['extensions'],
                        'minSize' => isset($config[$this->type]['minSize']) ? $config[$this->type]['minSize'] : 1024,
                        'maxSize' => isset($config[$this->type]['maxSize']) ? $config[$this->type]['maxSize'] : (1024 * 1024 * 200),
                    ]);
                    break;

                default:
                    // Is base64 format image
                    $model->addRule($attr, function ($attribute, $params) use ($model, $file) {
                        if (substr($file, 0, 5) != 'data:' || @imagecreatefromstring(ImageHelper::base64Decode($file)) === false) {
                            $model->addError($attribute, '无效的 Base64 图片文件。');
                        }
                    });
                    break;
            }
            if (!$model->validate()) {
                return $model;
            }
            $models[] = $model;
        }

        $successRes = [];
        foreach ($models as $i => $model) {
            /* @var $model DynamicModel */
            /* @var $file \yii\web\UploadedFile */
            $attr = $key . '_' . $i;
            $file = $model->$attr;
            $path = $request->getBaseUrl() . '/' . trim($config['path'], '/') . '/' . date('Ymd');
            $absolutePath = FileHelper::normalizePath(Yii::getAlias('@webroot') . $path);
            !file_exists($absolutePath) && FileHelper::createDirectory($absolutePath);
            $originalName = null;
            $extensionName = null;
            if ($validator == 'string') {
                $t = explode(';', $file);
                $mimeType = substr($t[0], 5);
                $extensionName = FileHelper::getExtensionsByMimeType($mimeType);
                $extensionName && $extensionName = $extensionName[0];

                $fileSize = 0;
            } else {
                $originalName = $file->name;
                $extensionName = $file->getExtension();
                if (empty($extensionName)) {
                    $extensionName = FileHelper::getExtensionsByMimeType($file->type);
                    $extensionName && $extensionName = $extensionName[0];
                }

                $fileSize = $file->size;
                $mimeType = FileHelper::getMimeTypeByExtension($extensionName);
            }
            empty($extensionName) && $extensionName = 'jpg';
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
                $res = [
                    'original_name' => $originalName,
                    'real_name' => $filename,
                    'path' => $path,
                    'full_path' => $request->getHostInfo() . $path,
                    'size' => $fileSize,
                    'mime_type' => $mimeType,
                ];
                if ($this->type == self::TYPE_IMAGE) {
                    $imgBinary = fread(fopen($absoluteSavePath, "r"), filesize($absoluteSavePath));
                    $imageBase64 = 'data:image/' . $mimeType . ';base64,' . base64_encode($imgBinary);
                    $res['base64'] = $imageBase64;
                }

                $successRes[$attr] = $res;
            } else {
                if ($successRes) {
                    foreach ($successRes as $r) {
                        FileHelper::unlink(Yii::getAlias('@webroot') . $r['path']);
                    }
                }
                $model->addError($attr, '文件上传失败。');

                return $model;
            }
        }

        if ($single) {
            return current($successRes);
        } else {
            return $successRes;
        }
    }

    /**
     * 删除文件
     *
     * @param $url
     * @return string
     * @throws NotFoundHttpException
     * @todo 需要判断文件所有者权限
     */
    public function actionDelete($url)
    {
        $url = parse_url($url);
        $file = $url['path'];
        $config = Yii::$app->params['uploading'];
        $dir = isset($config['dir']) ? $config['dir'] : "uploads";
        $path = Yii::getAlias("@webroot/$dir/" . ltrim($file, '\/'));
        if (file_exists($path)) {
            if (FileHelper::unlink($path)) {
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