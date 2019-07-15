<?php

namespace app\modules\api\controllers;

use app\modules\api\classes\Release;
use app\modules\api\classes\Releases;
use app\modules\api\extensions\BaseController;
use cebe\markdown\GithubMarkdown;
use yadjet\helpers\ImageHelper;
use Yii;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;

/**
 * 使用手册
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class ManualController extends BaseController
{

    /**
     * 手册目录
     *
     * @return false|mixed|string
     */
    public function actionIndex()
    {
        $json = file_get_contents(Yii::getAlias('@app/docs/manual/config.json'));
        $config = json_decode($json, true);
        if ($config === null) {
            return [];
        } else {
            foreach ($config as $k => &$v) {
                if ($k == 'directories') {
                    foreach ($v as $kk => &$vv) {
                        foreach ($vv as $kkk => &$vvv) {
                            if ($kkk == 'docs') {
                                $n = count($vvv);
                                foreach ($vvv as $kkkk => &$vvvv) {
                                    if ($kkkk == 0) {
                                        $v[$kk]['file'] = ($kk == 'default' ? "" : "$kk.") . $vvvv['name'];
                                    }
                                    $vvvv['file'] = ($kk == 'default' ? "" : "$kk.") . $vvvv['name'];
                                }
                                if ($n == 1) {
                                    $vvv = [];
                                }
                            } elseif ($kkk == 'children') {
                                // @todo
                            }
                        }
                    }
                }
            }
            $config['directories'] = array_values($config['directories']);
        }

        return $config;
    }

    /**
     * 手册详情展示
     *
     * @param null $file
     * @return array
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionView($file = null)
    {
        $file || $file = 'readme';
        $path = Yii::getAlias('@app/docs/manual/' . str_replace('.', '/', $file)) . '.md';
        if (file_exists($path) && ($content = file_get_contents($path)) !== false) {
            $content = (new GithubMarkdown())->parse($content);
            $images = ImageHelper::parseImages($content);
            if ($images) {
                $pairs = [];
                $destDir = Yii::getAlias('@webroot/manual-images');
                if (!file_exists($destDir)) {
                    FileHelper::createDirectory($destDir);
                }
                $sourceDir = Yii::getAlias('@app/docs/manual');
                $url = Yii::$app->getRequest()->getHostInfo();
                foreach ($images as $image) {
                    $sourceImage = FileHelper::normalizePath($sourceDir . '/' . trim($image, "/."), '/');
                    $extension = ImageHelper::getExtension($sourceImage);
                    $destFilename = md5($image) . '.' . $extension;
                    @copy($sourceImage, $destDir . '/' . $destFilename);
                    $pairs[$image] = "$url/manual-images/$destFilename";
                }
                $content = strtr($content, $pairs);
            }

            return [
                'content' => $content,
            ];
        } else {
            throw new NotFoundHttpException("`$file` 文件不存在。");
        }
    }

    /**
     * 系统发布日志
     *
     * @return array
     */
    public function actionReleases()
    {
        $releases = new Releases();
        $content = @file_get_contents(Yii::getAlias('@app/docs/releases/change.log.md'));
        if ($content !== false) {
            $items = preg_split('/\r\n\r\n/', $content);
            foreach ($items as $item) {
                $release = new Release();
                foreach (explode(PHP_EOL, $item) as $line) {
                    $line = trim($line);
                    if ($line) {
                        switch (strtolower($line[0])) {
                            case 'v':
                                preg_match('/(.*)\((.*)\)/', $line, $matches);
                                if (isset($matches[1])) {
                                    $release->setTitle($matches[1]);
                                }
                                if (isset($matches[2])) {
                                    $release->setDatetime($matches[2]);
                                }
                                break;

                            case "=":
                                break;

                            default:
                                $release->setItem($line);
                                break;
                        }
                    }
                }
                $releases->setItem($release);
            }
        }

        return $releases->getItems();
    }

}
