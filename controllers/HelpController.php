<?php

namespace app\controllers;

use cebe\markdown\GithubMarkdown;
use Yii;
use yii\helpers\FileHelper;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
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
        $docs = [];
        $markdown = new GithubMarkdown();
        $appPath = FileHelper::normalizePath(Yii::getAlias('@app'), '/');
        $files = FileHelper::findFiles($appPath . '/docs/' . $type, [
            'recursive' => false,
        ]);
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

            $navigation = [];
            $docs[$filename] = [
                'title' => $title,
                'content' => $content,
                'navigation' => $navigation
            ];
        }

        return $docs;
    }

    /**
     * @param string $type
     * @param null $file
     * @return string
     * @throws BadRequestHttpException
     */
    public function actionIndex($type = 'guide', $file = null)
    {
        $baseDir = Yii::getAlias('@app/docs');
        if (!file_exists($baseDir . "/" . $type)) {
            throw new BadRequestHttpException("`$type` is not exist.");
        }

        $sections = [];
        $docs = $this->getDocs($type);
        foreach ($docs as $key => $doc) {
            $sections[$key] = $doc['title'];
        }

        if (isset($docs[$file])) {
            $article = $docs[$file]['content'];
        } else {
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
