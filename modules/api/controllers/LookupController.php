<?php

namespace app\modules\api\controllers;

use app\models\Lookup;
use app\modules\api\extensions\BaseController;

/**
 * Class LookupController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class LookupController extends BaseController
{

    /**
     * 获取常规设定值
     *
     * @param $key
     * @param null $defaultValue
     * @return mixed|null
     * @throws \yii\db\Exception
     */
    public function actionValue($key, $defaultValue = null)
    {
        return Lookup::getValue($key, $defaultValue);
    }
}