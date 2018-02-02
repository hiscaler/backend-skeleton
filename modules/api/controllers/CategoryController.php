<?php

namespace app\modules\api\controllers;

use app\models\Category;
use yadjet\helpers\ArrayHelper;

/**
 * Class CategoryController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class CategoryController extends Controller
{

    /**
     * 分类数据
     *
     * @param null|string $sign
     * @param bool $sign
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionIndex($sign = null, $flat = true)
    {
        if ($sign) {
            $parentId = \Yii::$app->getDb()->createCommand('SELECT [[id]] FROM {{%category}} WHERE [[sign]] = :sign', [':sign' => $sign])->queryScalar();
            if (!$parentId) {
                return [];
            }
        } else {
            $parentId = 0;
        }
        $items = Category::getChildren($parentId);
        !$flat && $items = ArrayHelper::toTree($items, 'id', 'parent');

        return $items;
    }
}