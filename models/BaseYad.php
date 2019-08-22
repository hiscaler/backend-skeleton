<?php

namespace app\models;

use app\modules\admin\components\ApplicationHelper;
use Yii;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

class BaseYad
{

    /**
     * Return table name by special model name
     * For Example: Yad::modelName2TableName('app\models\news') return `news`, if
     * Use table prefix, will return `table_prefix_news` name
     *
     * @param string $modelName
     * @return string
     */
    public static function modelName2TableName($modelName)
    {
        $tableName = null;
        if (!empty($modelName)) {
            $tableName = (Yii::$app->getDb()->tablePrefix ?: '') . Inflector::camel2id(StringHelper::basename(ApplicationHelper::id2ClassName($modelName)), '_');
        }

        return $tableName;
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
