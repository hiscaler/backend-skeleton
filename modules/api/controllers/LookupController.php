<?php

namespace app\modules\api\controllers;

use app\models\Lookup;
use yii\web\NotFoundHttpException;

/**
 * Class CategoryController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class LookupController extends Controller
{

    /**
     * 获取常规设定值
     *
     * @param $key
     * @return mixed|null
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionValue($key)
    {
        $value = Lookup::getValue($key);
        if ($value === null) {
            throw new NotFoundHttpException("$key 值不存在。");
        }

        return $value;
    }
}