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

        foreach (\app\models\Module::map() as $alias => $name) {
            $path = $appPath . "/modules/api/modules/$alias/docs";
            if (file_exists("$path/$type/")) {
                $searchDirs[$alias][] = "$path/$type/";
            }
        }

        $filesList = [];
        foreach ($searchDirs as $key => $dir) {
            if (!isset($filesList[$key])) {
                $filesList[$key] = [];
            }
            foreach ($dir as $name => $d) {
                $rawFiles = FileHelper::findFiles($d, [
                    'recursive' => false,
                ]);
                if ($rawFiles) {
                    $filesList[$key] = array_merge($filesList[$key], $rawFiles);
                }
            }
        }
        foreach ($filesList as $key => $files) {
            foreach ($files as $file) {
                $file = FileHelper::normalizePath($file, '/');
                $content = file_get_contents($file);
                if ($content === false) {
                    continue;
                }

                $filename = basename($file, '.md');
                if ($type == 'guide' && $filename == 'readme') {
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
    public function actionIndex($type = 'db-dict', $file = null)
    {
        $baseDir = Yii::getAlias('@app/docs');
        if (!in_array($type, ['db-dict', 'guide', 'api'])) {
            $type = 'db-dict';
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
