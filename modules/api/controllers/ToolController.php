<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use BadFunctionCallException;
use stdClass;
use Yii;
use yii\helpers\FileHelper;
use yii\web\NotFoundHttpException;

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
        $dirPrefix = Yii::getAlias('@runtime');
        if (!$dir) {
            $dirs = FileHelper::findDirectories($dirPrefix, ['recursive' => false]);
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
     * @return void
     * @throws NotFoundHttpException
     */
    public function actionLogView($name = 'app')
    {
        $filename = Yii::getAlias("@runtime/logs/$name.log");
        if (file_exists($filename)) {
            $handle = @fopen($filename, "r");
            if ($handle) {
                while (($buffer = fgets($handle)) !== false) {
                    echo "$buffer </br>";
                }
                if (!feof($handle)) {
                    throw new BadFunctionCallException('Unexpected fail.');
                }
                fclose($handle);
            }
        } else {
            throw new NotFoundHttpException("Not Found $name log file.");
        }
    }

}