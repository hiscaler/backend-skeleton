<?php

namespace app\modules\api\controllers;

use app\helpers\Uploader;
use app\modules\api\extensions\AuthController;
use app\modules\api\models\Constant;
use Imagine\Image\ManipulatorInterface;
use RuntimeException;
use stdClass;
use yadjet\helpers\ImageHelper;
use yadjet\helpers\UrlHelper;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\FileHelper;
use yii\imagine\Image;
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
class FileController extends AuthController
{

    /**
     * 类型
     */
    const TYPE_IMAGE = 'image';
    const TYPE_FILE = 'file';

    /**
     * 文件格式
     */
    const FORMAT_UPLOADED_FILE = 'uploaded-file';
    const FORMAT_BASE64 = 'base64';
    const FORMAT_NETWORK_FILE = 'network-file';

    protected $type = self::TYPE_FILE;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!isset(Yii::$app->params['upload'])) {
            throw new InvalidConfigException('无效的上传配置。');
        }
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'uploading' => ['POST'],
                    'delete' => ['DELETE'],
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
    }

    /**
     * 文件上传
     *
     * @rbacDescription 文件上传
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
        $config = Yii::$app->params['upload'];
        if (!isset($config[$this->type])) {
            throw new InvalidConfigException("无效的 upload.$this->type 上传配置。");
        }

        $request = Yii::$app->getRequest();
        $files = UploadedFile::getInstancesByName($key);
        if (!$files) {
            $files = $request->post($key);
            !$files && $files = $request->get($key);
            if ($files && !is_array($files)) {
                $files = [$files];
            }
        }
        if (empty($files)) {
            throw new BadRequestHttpException("未检测到表单元素名称为`$key`提交的数据。");
        }

        $models = [];
        foreach ($files as $i => $file) {
            $attr = $key . '_' . $i;
            $model = new DynamicModel([$attr, 'type', 'file_format', 'generate_thumbnail', 'thumbnail_size', 'thumbnail_mode']);
            $model->load(Yii::$app->getRequest()->getBodyParams(), '');
            $model->type = $this->type;
            $model->addRule('type', 'string');
            $model->addRule('file_format', 'safe');
            $model->addRule('type', 'in', ['range' => ['file', 'image']]);
            $model->$attr = $file;
            $fileFormat = null;
            if ($file instanceof UploadedFile) {
                $fileFormat = self::FORMAT_UPLOADED_FILE;
            } elseif (is_string($file) && substr($file, 0, 5) == 'data:') {
                $fileFormat = self::FORMAT_BASE64;
            } elseif (is_string($file) && (substr($file, 0, 4) == 'http') || substr($file, 0, 2) == '//') {
                $fileFormat = self::FORMAT_NETWORK_FILE;
            } else {
                $fileFormat = null;
            }
            $model->file_format = $fileFormat;
            switch ($fileFormat) {
                case self::FORMAT_UPLOADED_FILE:
                    $model->addRule($attr, $this->type, [
                        'skipOnEmpty' => false,
                        'enableClientValidation' => false,
                        'extensions' => $config[$this->type]['extensions'],
                        'minSize' => isset($config[$this->type]['minSize']) ? $config[$this->type]['minSize'] : 1024,
                        'maxSize' => isset($config[$this->type]['maxSize']) ? $config[$this->type]['maxSize'] : (1024 * 1024 * 200),
                    ]);
                    break;

                case self::FORMAT_NETWORK_FILE:
                    // Network resource
                    $model->addRule($attr, function ($attribute, $params) use ($model, $file) {
                        if (substr($file, 0, 4) != 'http' && substr($file, 0, 2) != '//') {
                            $model->addError($attribute, '无效的网络图片文件。');
                        }
                    });
                    break;

                case self::FORMAT_BASE64:
                    // Is base64 format image
                    $model->addRule($attr, function ($attribute, $params) use ($model, $file) {
                        if (substr($file, 0, 5) != 'data:' || @imagecreatefromstring(ImageHelper::base64Decode($file)) === false) {
                            $model->addError($attribute, '无效的 Base64 图片文件。');
                        }
                    });
                    break;

                default:
                    $model->addError($attr, '未知的上传对象。');
                    break;
            }
            if ($this->type == self::TYPE_IMAGE) {
                // 缩略图相关设置验证规则
                $model->addRule('generate_thumbnail', 'boolean');
                $model->addRule('generate_thumbnail', 'default', ['value' => Constant::BOOLEAN_TRUE]);
                $model->addRule('thumbnail_size', 'string', ['min' => 3]);
                $model->addRule('thumbnail_size', 'trim');
                $model->addRule('thumbnail_size', 'default', ['value' => '100x200']);
                $model->addRule('thumbnail_size', 'required');
                $model->addRule('thumbnail_size', function ($attribute, $params) use ($model) {
                    $hasError = false;
                    $model->$attribute = strtolower($model->$attribute);
                    if (stripos($model->$attribute, 'x') === false) {
                        $hasError = true;
                    } else {
                        list($width, $height) = explode('x', $model->$attribute);
                        if (!filter_var($width, FILTER_VALIDATE_INT) || !filter_var($height, FILTER_VALIDATE_INT) || $width <= 0 || $height <= 0) {
                            $hasError = true;
                        }
                    }
                    if ($hasError) {
                        $model->addError($attribute, '无效的缩略图设置（例如：100x200）。');
                    }
                });
                $model->addRule('thumbnail_mode', 'string', ['min' => 1]);
                $model->addRule('thumbnail_mode', 'trim');
                $model->addRule('thumbnail_mode', 'default', ['value' => 'outbound']);
                $model->addRule('thumbnail_mode', 'required');
                $model->addRule('thumbnail_mode', 'in', ['range' => [
                    ManipulatorInterface::THUMBNAIL_INSET,
                    ManipulatorInterface::THUMBNAIL_OUTBOUND,
                ]]);
            }
            if (!$model->validate()) {
                return $model;
            }
            $models[] = $model;
        }

        $successRes = [];
        $uploader = new Uploader();
        foreach ($models as $i => $model) {
            /* @var $model DynamicModel */
            /* @var $file \yii\web\UploadedFile */
            $attr = $key . '_' . $i;
            $file = $model->$attr;
            $originalName = null;
            $extensionName = null;
            if ($model->file_format == self::FORMAT_BASE64) {
                $t = explode(';', $file);
                $mimeType = substr($t[0], 5);
                $extensionName = FileHelper::getExtensionsByMimeType($mimeType);
                $extensionName && $extensionName = $extensionName[0];

                $fileSize = 0;
            } elseif ($model->file_format == self::FORMAT_UPLOADED_FILE) {
                $originalName = $file->name;
                $extensionName = $file->getExtension();
                if (empty($extensionName)) {
                    $extensionName = FileHelper::getExtensionsByMimeType($file->type);
                    $extensionName && $extensionName = $extensionName[0];
                }

                $fileSize = $file->size;
                $mimeType = FileHelper::getMimeTypeByExtension($extensionName);
            } elseif ($model->file_format == self::FORMAT_NETWORK_FILE) {
                $extensionName = ImageHelper::getExtension($file);
                $mimeType = FileHelper::getMimeTypeByExtension($extensionName);
                $fileSize = 0;
            } else {
                $fileSize = 0;
                $mimeType = null;
            }
            empty($extensionName) && $extensionName = 'jpg';
            $uploader->setFilename(null, $extensionName);

            switch ($model->file_format) {
                case self::FORMAT_UPLOADED_FILE:
                    $success = $file->saveAs($uploader->getPath());
                    break;

                case self::FORMAT_BASE64:
                    $success = file_put_contents($uploader->getPath(), ImageHelper::base64Decode($file));
                    $fileSize = filesize($uploader->getPath());
                    break;

                case self::FORMAT_NETWORK_FILE:
                    $content = file_get_contents($file);
                    if ($content !== false) {
                        $success = file_put_contents($uploader->getPath(), $content);
                        $fileSize = filesize($uploader->getPath());
                    } else {
                        $success = false;
                    }
                    break;

                default:
                    $success = false;
                    break;
            }

            if ($success) {
                $res = [
                    'original_name' => $originalName,
                    'real_name' => $uploader->getFilename(),
                    'path' => $uploader->getUrl(),
                    'full_path' => $request->getHostInfo() . $uploader->getUrl(),
                    'size' => $fileSize,
                    'mime_type' => $mimeType,
                ];
                if ($model->type == self::TYPE_IMAGE) {
                    if ($model->generate_thumbnail) {
                        list($width, $height) = explode('x', $model->thumbnail_size);
                        $thumb = Image::thumbnail($uploader->getPath(), $width, $height, $model->thumbnail_mode);
                        $thumbnailFilename = ImageHelper::generateThumbnailFilename($uploader->getFilename());
                        if ($thumbnailFilename) {
                            $uploader->setFilename($thumbnailFilename);
                        } else {
                            $uploader->setFilename(null, $extensionName);
                        }
                        $thumb->save($uploader->getPath());
                        $res['thumbnail'] = [
                            'real_name' => $uploader->getFilename(),
                            'path' => $uploader->getUrl(),
                            'full_path' => $request->getHostInfo() . $uploader->getUrl(),
                            'base64' => ImageHelper::base64Encode($uploader->getPath(), $mimeType),
                        ];
                    } else {
                        $res['base64'] = ImageHelper::base64Encode($uploader->getPath(), $mimeType);
                    }
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
     * @rbacDescription 文件删除
     * @param $url
     * @return stdClass
     * @throws NotFoundHttpException
     * @todo 需要判断文件所有者权限
     */
    public function actionDelete($url)
    {
        $parseUrl = parse_url($url);
        $file = $parseUrl['path'];
        $path = Yii::getAlias("@webroot" . '/' . ltrim($file, '\/'));
        if (file_exists($path)) {
            if (FileHelper::unlink($path)) {
                Yii::$app->getResponse()->setStatusCode(200);

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