<?php

namespace app\modules\api\controllers;

use app\modules\api\classes\Release;
use app\modules\api\classes\Releases;
use app\modules\api\extensions\BaseController;
use cebe\markdown\GithubMarkdown;
use Yii;
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
     */
    public function actionView($file = null)
    {
        $file || $file = 'readme';
        $path = Yii::getAlias('@app/docs/manual/' . str_replace('.', '/', $file)) . '.md';
        if (file_exists($path) && ($content = file_get_contents($path)) !== false) {
            $article = (new GithubMarkdown())->parse($content);

            return [
                'content' => $article,
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
