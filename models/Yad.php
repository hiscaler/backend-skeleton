<?php

namespace app\models;

use Yii;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

class Yad
{

    /**
     * Return table name by special model name
     * For Example: Yad::modelName2TableName('app\models\news') return `news`, if
     * Use table prefix, will return `table_prifix_news` name
     *
     * @param string $modelName
     * @return string
     */
    public static function modelName2TableName($modelName)
    {
        $tableName = null;
        if (!empty($modelName)) {
            $tableName = (Yii::$app->getDb()->tablePrefix ?: '') . Inflector::camel2id(StringHelper::basename(BaseActiveRecord::id2ClassName($modelName)), '_');
        }

        return $tableName;
    }

    /**
     * 获取文本内容中的所有图片路径
     *
     * @param string $content
     * @param string|integer $order
     * @return array|string|null
     */
    public static function getTextImages($content, $order = 'ALL')
    {
        $images = [];
        if (!empty($content)) {
            $pattern = "/<img.*?src=[\'|\"](.*?)[\'|\"].*?[\/]?>/";
            preg_match_all($pattern, $content, $match);
            if (isset($match[1]) && !empty($match[1])) {
                if ($order === 'ALL') {
                    $images = $match[1];
                }
                if (is_numeric($order) && isset($match[1][$order - 1])) {
                    $images = $match[1][$order - 1];
                }
            }
        }

        return $images;
    }

    /**
     * 系统版本
     *
     * @return string
     */
    public static function getVersion()
    {
        return '0.0.1';
    }

}
