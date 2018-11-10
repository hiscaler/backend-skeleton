<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use BadFunctionCallException;
use stdClass;
use Yii;
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
            $content = file_get_contents($filename);
            if ($content !== false) {
                return new Response([
                    'format' => Response::FORMAT_RAW,
                    'data' => $content,
                ]);
            } else {
                throw new BadFunctionCallException("读取日志文件出错。");
            }
        } else {
            throw new NotFoundHttpException("Not Found $name log file.");
        }
    }

}