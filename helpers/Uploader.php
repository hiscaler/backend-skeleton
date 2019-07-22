<?php

namespace app\helpers;

use yadjet\helpers\StringHelper;
use Yii;
use yii\helpers\FileHelper;

/**
 * 文件上传路径以及目录处理类
 *
 * @package app\helpers
 * @author hiscaler <hiscaler@gmail.com>
 */
class Uploader
{

    /**
     * @var string 文件名
     */
    private $filename = null;

    /**
     * @var string url 前缀
     */
    private $url = null;

    /**
     * @var string 目录路径前缀
     */
    private $path = null;

    /**
     * Uploader constructor.
     *
     * @throws \yii\base\Exception
     */
    public function __construct()
    {
        $dir = Config::get('upload.dir', 'uploads');
        $dir = trim($dir, "/\\\\"); // 移除头尾的 "/", "\"
        $url = FileHelper::normalizePath("/$dir/" . date('Y') . '/' . date('n') . '/' . date('j'), '/');
        $this->url = $url;

        $this->path = Yii::getAlias('@webroot') . $url;
        if (!file_exists($this->path)) {
            FileHelper::createDirectory($this->path);
        }
    }

    /**
     * 设置要处理的文件
     *
     * @param $filename
     * @param null|string $extension
     * @throws \Exception
     */
    public function setFilename($filename = null, $extension = null)
    {
        if (!$filename) {
            $filename = self::generateFilename($extension);
        }
        $this->filename = trim($filename);
    }

    /**
     * 获取操作的文件名
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * 文件路径
     * 例如：/uploads/2019/2/3/sample.jpg
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url . '/' . $this->filename;
    }

    /**
     * 获取全路径
     * 例如：/www/uploads/2019/2/3/sample.jpg
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path . '/' . $this->filename;
    }

    /**
     *  生成文件名称
     *
     * @param $extension
     * @return string
     * @throws \Exception
     */
    public static function generateFilename($extension)
    {
        $filename = md5(StringHelper::generateRandomString() . uniqid('', true) . mt_rand());
        if ($extension && $extension[0] != '.') {
            $extension = ".$extension";
        }

        return "{$filename}{$extension}";
    }

}