<?php

namespace app\modules\admin\components;

use Yii;

/**
 * Query Condition Cache class, Use Cache class to set, get or remove query conditions.
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class QueryConditionCache
{

    /**
     * Set to cache
     *
     * @param string $key
     * @param mixed $conditions
     * @return mixed
     */
    public static function set($key, $conditions)
    {
        if (!empty($key)) {
            Yii::$app->getCache()->set(strtolower(self::parseKey($key)), $conditions);
        } else {
            return;
        }
    }

    /**
     * Get from cache
     *
     * @param string $key
     * @return mixed
     */
    public static function get($key)
    {
        if (!empty($key)) {
            $value = Yii::$app->getCache()->get(self::parseKey($key));

            return ($value != false) ? $value : null;
        } else {
            return null;
        }
    }

    /**
     * Remove it by key
     *
     * @param string $key
     */
    public static function remove($key)
    {
        if (!empty($key)) {
            Yii::$app->getCache()->delete(self::parseKey($key));
        }
    }

    /**
     * Parse key, if key is not empty, then add 'query.condition.' prefix
     *
     * @param string $key
     * @return string
     */
    private static function parseKey($key)
    {
        return strtolower("cache.data.query.condition.{$key}." . \Yii::$app->getUser()->getId());
    }

}
