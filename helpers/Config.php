<?php

namespace app\helpers;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * 配置参数读取
 * 仅对 config/params 配置文件进行读取
 *
 * @package app\helpers
 * @author hiscaler <hiscaler@gmail.com>
 */
class Config
{

    /**
     * 是否存在指定的配置项
     *
     * @param $key
     * @return bool
     */
    public static function exists($key)
    {
        $exists = false;
        $params = Yii::$app->params;
        if (array_key_exists($key, $params)) {
            $exists = true;
        } elseif (strpos($key, '.') !== false) {
            $levels = explode('.', $key);
            $n = count($levels) - 1;
            foreach ($levels as $i => $level) {
                if (array_key_exists($level, $params)) {
                    $params = $params[$level];
                    if ($i == $n) {
                        $exists = true;
                    }
                } else {
                    break;
                }
            }
        }

        return $exists;
    }

    /**
     * 获取配置参数值
     *
     * @param $key
     * @param null $defaultValue
     * @return mixed|null
     */
    public static function get($key, $defaultValue = null)
    {
        $params = Yii::$app->params;
        if (isset($params[$key])) {
            $value = $params[$key];
        } elseif (strpos($key, '.') !== false) {
            $value = ArrayHelper::getValue($params, $key, $defaultValue);
        } else {
            $value = $defaultValue;
        }

        return $value;
    }

}
