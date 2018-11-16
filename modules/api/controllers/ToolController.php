<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use BadFunctionCallException;
use stdClass;
use Yii;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 实用工具
 * Class FileController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class ToolController extends BaseController
{

    /**
     * 清理 runtime 目录
     *
     * @param null $dir
     * @return stdClass
     * @throws \yii\base\ErrorException
     */
    public function actionFlushRuntime($dir = null)
    {
        ignore_user_abort(true);
        ini_set('max_execution_time', 60);
        $dirPrefix = Yii::getAlias('@runtime');
        if (!$dir) {
            $dirs = [];
            $rootDirs = FileHelper::findDirectories($dirPrefix, ['recursive' => false]);
            foreach ($rootDirs as $d) {
                $dirs = array_merge($dirs, FileHelper::findDirectories($d) ?: [$d]);
            }
        } else {
            $dirs = [];
            foreach ($dir as $d) {
                if (file_exists("$dirPrefix/$d")) {
                    $dirs[] = "$dirPrefix/$d";
                }
            }
        }

        foreach ($dirs as $dir) {
            FileHelper::removeDirectory($dir);
        }

        return new stdClass();
    }

    /**
     * 清理缓存
     *
     * @param null $key
     * @return stdClass
     */
    public function actionFlushCache($key = null)
    {
        if ($key) {
            Yii::$app->getCache()->delete($key);
        } else {
            Yii::$app->getCache()->flush();
        }

        return new stdClass();
    }

    /**
     * 查看日志文件
     *
     * @param string $name
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionLogView($name = 'app')
    {
        $filename = Yii::getAlias("@runtime/logs/$name.log");
        if (file_exists($filename)) {
            $rows = [];
            $handle = @fopen($filename, "r");
            if ($handle) {
                $key = 0;
                while (($buffer = fgets($handle)) !== false) {
                    $buffer = trim($buffer);
                    if (empty($buffer)) {
                        continue;
                    }

                    $category = $title = $row = null;
                    if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $buffer)) {
                        $key++;
                        $title = null;
                        if (preg_match('/^[\d\-\: ]+\[.*\]\[.*\]\[.*\]\[(.*)\]\[(.*)\](.*)$/U', $buffer, $matches)) {
                            $category = trim($matches[1]);
                            $title = trim($matches[2]);
                            $row = $matches[3];
                        }
                    } else {
                        $row = $buffer;
                    }

                    $title && !isset($rows[$key]['title']) && $rows[$key]['title'] = $title;
                    $category && !isset($rows[$key]['category']) && $rows[$key]['category'] = $category;
                    $rows[$key]['rows'][] = $row;
                }
                if (!feof($handle)) {
                    throw new BadFunctionCallException('Unexpected fail.');
                }
                fclose($handle);
            }

            $output = '';
            foreach ($rows as $key => $row) {
                $output .= '<h2 class="' . $row['category'] . '">' . "{$row['category']}<span>{$row['title']}</span></h2>";
                $output .= '<div class="traces"><pre>' . implode(PHP_EOL, $row['rows']) . '</pre></div>';
            }
            $html = <<<EOT
<!DOCTYPE html>
<html><head><meta charset="utf-8"><title>Logger</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <meta name="apple-mobile-web-app-status-bar-style" content="black"> 
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="format-detection" content="telephone=no">
  <style type="text/css">
    *, body { font-size: 14px; margin: 0; padding: 0; }
    h2 { font-size: 20px; font-weight: bold; height: 40px; line-height: 40px; color: #FFF; padding-left: 10px; border-radius: 6px 6px 0 0; margin: 0 10px; }
    h2 span { font-size: 14px; margin-left: 20px; }
    h2.error { background-color: red; }
    h2.warning { background-color: gold; }
    h2.info { background-color: grey; }
    div.traces { background: #CCC; padding: 10px; border-radius: 0 0 6px 6px; margin: 0 10px 5px 10px; word-break: break-all; word-wrap: break-spaces }
  </style>
</head><body>$output</body></html>
EOT;

            return new Response([
                'format' => Response::FORMAT_RAW,
                'data' => $html,
            ]);
        } else {
            throw new NotFoundHttpException("Not Found $name log file.");
        }
    }

}