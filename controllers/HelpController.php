<?php

namespace app\controllers;

use cebe\markdown\GithubMarkdown;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\Response;

/**
 * 帮助文档展示
 * Class HelpController
 *
 * @package app\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class HelpController extends \yii\web\Controller
{

    /**
     * 文档类型
     */
    const TYPE_API = 'api';
    const TYPE_GUIDE = 'guide';
    const TYPE_DB_DICT = 'db-dict';

    public function init()
    {
        parent::init();
        Yii::$app->setComponents([
            'response' => [
                'class' => Response::class,
                'format' => Response::FORMAT_HTML,
            ],
        ]);
    }

    private function getDocs($type)
    {
        $appPath = FileHelper::normalizePath(Yii::getAlias('@app'), '/');
        $searchDirs = [
            '_' => [
                $appPath . "/docs/$type"
            ],
        ];
        $docs = [];
        $markdown = new GithubMarkdown();
        $appPath = FileHelper::normalizePath(Yii::getAlias('@app'), '/');

        if ($type != self::TYPE_DB_DICT) {
            foreach (\app\models\Module::map() as $alias => $name) {
                if ($type == self::TYPE_API) {
                    $path = $appPath . "/modules/api/modules/$alias/docs";
                } else {
                    $path = $appPath . "/modules/admin/modules/$alias/docs";
                }

                if (file_exists($path)) {
                    $searchDirs[$alias][] = $path;
                }
            }
        }

        foreach ($searchDirs as $key => $dir) {
            $files = [];
            foreach ($dir as $name => $d) {
                $findFiles = FileHelper::findFiles($d, [
                    'recursive' => false,
                ]);
                if ($findFiles) {
                    $files = array_merge($files, $findFiles);
                }
            }
            foreach ($files as $file) {
                $file = FileHelper::normalizePath($file, '/');
                $content = file_get_contents($file);
                if ($content === false) {
                    continue;
                }

                $filename = basename($file, '.md');
                if ($type == self::TYPE_GUIDE && $filename == 'readme') {
                    continue;
                }
                $content = $markdown->parse($content);
                preg_match('/<h1>(.*)<\/h1>?.*/', $content, $matches);
                if ($matches) {
                    $title = $matches[1];
                } else {
                    $title = $filename;
                }

                $docs[$key][$filename] = [
                    'title' => $title,
                    'content' => $content,
                ];
            }
        }

        return $docs;
    }

    /**
     * @param string $type
     * @param null $file
     * @return string
     */
    public function actionIndex($type = self::TYPE_DB_DICT, $file = null)
    {
        $baseDir = Yii::getAlias('@app/docs');
        if (!in_array($type, [self::TYPE_API, self::TYPE_GUIDE, self::TYPE_DB_DICT])) {
            $type = self::TYPE_DB_DICT;
        }

        $sections = [];
        $searchPath = null;
        if ($file) {
            if (stripos($file, '.') === false) {
                $searchPath = "_.$file";
            } else {
                $searchPath = $file;
            }
        }

        $docs = $this->getDocs($type);

        foreach ($docs as $key => $doc) {
            if (!isset($sections[$key])) {
                $sections[$key] = [];
            }
            foreach ($doc as $k => $v) {
                $sections[$key][$k] = $v['title'];
            }
        }

        $article = ArrayHelper::getValue($docs, "$searchPath.content");

        if (!$article) {
            $readmeFile = $baseDir . "/$type/readme.md";
            if (!file_exists($readmeFile)) {
                $readmeFile = "$baseDir/readme.md";
            }
            $content = file_get_contents($readmeFile);
            $article = (new GithubMarkdown())->parse($content);
        }

        return $this->render('index', [
            'type' => $type,
            'file' => $file,
            'sections' => $sections,
            'article' => $article,
        ]);
    }

}
