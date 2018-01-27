<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class Option
{

    /**
     * Status values
     */
    const STATUS_DRAFT = 0;
    const STATUS_PENDING = 1;
    const STATUS_PUBLISHED = 2;
    const STATUS_DELETED = 3;
    const STATUS_ARCHIVED = 4;

    /**
     * Boolean options
     *
     * @return array
     */
    public static function booleanOptions()
    {
        return [
            Constant::BOOLEAN_FALSE => Yii::t('app', 'No'),
            Constant::BOOLEAN_TRUE => Yii::t('app', 'Yes'),
        ];
    }

    /**
     * 排序下拉列表框数据
     *
     * @param integer $start
     * @param integer $max
     * @return array
     */
    public static function orderingOptions($start = 0, $max = 60)
    {
        $options = [];
        for ($i = $start; $i <= $max; $i++) {
            $options[$i] = $i;
        }

        return $options;
    }

    /**
     * Data status values
     *
     * @return array
     */
    public static function statusOptions()
    {
        return [
            self::STATUS_DRAFT => Yii::t('app', 'Draft'),
            self::STATUS_PENDING => Yii::t('app', 'Pending'),
            self::STATUS_PUBLISHED => Yii::t('app', 'Published'),
            self::STATUS_DELETED => Yii::t('app', 'Deleted'),
            self::STATUS_ARCHIVED => Yii::t('app', 'Archived')
        ];
    }

    /**
     * 性别选项
     *
     * @return array
     */
    public static function sexes()
    {
        return [
            Constant::SEX_UNKNOWN => '未知',
            Constant::SEX_MALE => '男',
            Constant::SEX_FEMALE => '女',
        ];
    }

    /**
     * 获取所有表名称
     *
     * @return string[]
     * @throws \yii\base\NotSupportedException
     */
    public static function tables()
    {
        return Yii::$app->getDb()->getSchema()->getTableNames('', true);
    }

    /**
     * 获取所有数据模型
     *
     * @return array
     * @throws \yii\base\NotSupportedException
     */
    public static function models()
    {
        $models = [];
        $tablePrefix = \Yii::$app->getDb()->tablePrefix;
        $coreTables = ['category', 'entity_label', 'file_upload_config', 'grid_column_config', 'label', 'lookup', 'member', 'meta', 'meta_validator', 'meta_value', 'migration', 'module', 'user', 'user_auth_category', 'user_credit_log', 'user_group', 'user_login_log', 'wechat_member'];
        $path = Yii::getAlias('@app');
        foreach (self::tables() as $table) {
            $table = str_replace($tablePrefix, '', $table);
            if ($table == 'migration') {
                continue;
            }
            $modelName = Inflector::id2camel($table, '_');
            if (in_array($table, $coreTables) && file_exists($path . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $modelName . '.php')) {
                $models[$table] = "app\\model\\$modelName";
            } else {
                $index = stripos($table, '_');
                if ($index === false) {
                    $moduleName = $modelName = $table;
                } else {
                    $moduleName = substr($table, 0, $index);
                    $modelName = substr($table, $index + 1);
                }
                $moduleName = strtolower($moduleName);
                $modelName = Inflector::id2camel($modelName, '_');
                if ($moduleName !== false && file_exists($path . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $modelName . '.php')) {
                    $models[$table] = "app\\modules\\admin\\modules\\$moduleName\\models\\$modelName";
                }
            }
        }

        return $models;
    }

}
