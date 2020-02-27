<?php

namespace app\modules\admin\controllers;

use app\models\Module;
use cebe\markdown\GithubMarkdown;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;

/**
 * 帮助中心
 * Class HelpController
 *
 * @package app\modules\admin\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class HelpController extends \yii\web\Controller
{

    private function getDocs()
    {
        $docs = [];
        $markdown = new GithubMarkdown();
        $appPath = FileHelper::normalizePath(Yii::getAlias('@app'), '/');
        $files = FileHelper::findFiles($appPath . '/docs');
        foreach (Module::map() as $alias => $name) {
            $path = $appPath . "/modules/api/modules/{$alias}/docs";
            if (file_exists($path)) {
                $files = array_merge($files, FileHelper::findFiles($path));
            }
        }
        foreach ($files as $file) {
            $file = FileHelper::normalizePath($file, '/');
            $content = file_get_contents($file);
            if ($content === false) {
                continue;
            }

            $filename = basename($file, '.md');
            if (strpos($file, $appPath . '/docs/db-dict/') === 0) {
                $filename = 'db-dict.' . $filename;
            }
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
            if (strpos($filename, 'db-dict.') === 0) {
                if (!isset($docs['db-dict'])) {
                    $docs['db-dict'] = [
                        'title' => '数据字典',
                    ];
                } else {
                    $filename = substr($filename, 8);
                    $docs['db-dict']['items'][$filename] = [
                        'title' => $title,
                        'content' => $content,
                        'navigation' => $navigation
                    ];
                }
            } else {
                $docs[$filename] = [
                    'title' => $title,
                    'content' => $content,
                    'navigation' => $navigation
                ];
            }
        }

        return $docs;
    }

    /**
     * 文档查看
     *
     * @rbacIgnore true
     * @param string $file
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex($file = 'about')
    {
        $docs = $this->getDocs();
        $fileKey = $file;
        if (strpos($fileKey, '.') !== false) {
            list($d1, $d2) = explode('.', $fileKey);
            if ($d1 == 'db-dict') {
                $fileKey = "{$d1}.items.{$d2}";
            } else {
                $fileKey = "$d1.$d2";
            }
        }
        if (ArrayHelper::getValue($docs, $fileKey)) {
            $this->layout = false;
            $sections = [];
            foreach ($docs as $key => $doc) {
                $sections[$key] = [
                    'label' => $doc['title'],
                    'url' => ['help/index', 'file' => $key],
                    'active' => $file == $key,
                ];
                if (isset($doc['items']) && $doc['items']) {
                    foreach ($doc['items'] as $k => $v) {
                        $sections[$key]['items'][] = [
                            'label' => $v['title'],
                            'url' => ['help/index', 'file' => "$key.$k"],
                            'active' => $file == "$key.$k",
                        ];
                    }
                    $sections[$key]['url'] = $sections[$key]['items'][0]['url'];
                }
            }

            return $this->render('index.php', [
                'sections' => $sections,
                'doc' => ArrayHelper::getValue($docs, $fileKey)
            ]);
        }

        throw new NotFoundHttpException("$file 文档不存在。");
    }

}