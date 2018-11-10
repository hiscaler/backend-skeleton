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

    /**
     * 获取多个常规设定值
     * a:1,b:2,c 会转换为：['a' => 1, 'b' => 2, c]
     *
     * @param $keys
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionValues($keys)
    {
        $params = [];
        foreach (explode(',', $keys) as $value) {
            if (stripos($value, ':', $value) === false) {
                $params[] = $value;
            } else {
                list($key, $value) = explode(':', $value);
                $params[$key] = $value;
            }
        }

        return Lookup::getValues($params);
    }

}