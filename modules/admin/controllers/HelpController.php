<?php

namespace app\modules\admin\controllers;

use cebe\markdown\GithubMarkdown;
use Yii;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;

/**
 * 帮助中心
 *
 * @package app\modules\admin\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class HelpController extends Controller
{

    private function getDocs()
    {
        $docs = [];
        $markdown = new GithubMarkdown();
        $appPath = Yii::getAlias('@app');
        $files = FileHelper::findFiles($appPath . '/docs');
        foreach (\app\models\Module::getModules() as $alias => $name) {
            $path = $appPath . "/modules/api/modules/{$alias}/docs";
            if (file_exists($path)) {
                $files = array_merge($files, FileHelper::findFiles($path));
            }
        }
        foreach ($files as $file) {
            $content = file_get_contents($file);
            if ($content === false) {
                continue;
            }
            $filename = basename($file, '.md');
            preg_match("/modules\/api\/modules\/(\w.*)\/docs/", $file, $matches);
            if ($matches) {
                $filename = $matches[1] . ".$filename";
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
     * 文档查看
     *
     * @param string $file
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($file = 'about')
    {
        $docs = $this->getDocs();
        if (isset($docs[$file])) {
            $this->layout = false;
            $sections = [];
            foreach ($docs as $key => $doc) {
                $sections[] = [
                    'label' => $doc['title'],
                    'url' => ['help/index', 'file' => $key],
                ];
            }

            return $this->render('index.php', [
                'sections' => $sections,
                'doc' => $docs[$file]
            ]);
        }

        throw new NotFoundHttpException("$file 文档不存在。");
    }
}