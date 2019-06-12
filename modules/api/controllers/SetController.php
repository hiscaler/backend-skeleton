<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\models\Set;

/**
 * Class SetController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class SetController extends ActiveController
{

    public $modelClass = Set::class;

    public function actions()
    {
        return [];
    }

    /**
     * 获取值
     *
     * @param $key
     * @param null $defaultValue
     * @return mixed|null
     * @throws \yii\db\Exception
     */
    public function actionGet($key, $defaultValue = null)
    {
        return Set::get($key, $defaultValue);
    }

}