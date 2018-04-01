<?php

namespace app\models;

use Yii;
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
    public static function boolean()
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
    public static function ordering($start = 0, $max = 60)
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
    public static function status()
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
     * @param bool $namespace value 值是否为模型的命名空间地址
     * @return array
     * @throws \yii\base\NotSupportedException
     */
    public static function models($namespace = false)
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
                if ($namespace) {
                    $models[$table] = "app\\models\\$modelName";
                } else {
                    $models[$table] = Yii::t('model', Inflector::camel2words($modelName));
                }
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
                    if ($namespace) {
                        $models[$table] = "app\\modules\\admin\\modules\\$moduleName\\models\\$modelName";
                    } else {
                        $models[$table] = "$moduleName: " . Yii::t("$moduleName", Inflector::camel2words($modelName));
                    }
                }
            }
        }

        natsort($models);

        return $models;
    }

    public static function languages()
    {
        return [
            'ar' => Yii::t('language', 'ar'),
            'bg' => Yii::t('language', 'bg'),
            'ca' => Yii::t('language', 'ca'),
            'da' => Yii::t('language', 'da'),
            'de' => Yii::t('language', 'de'),
            'en-US' => Yii::t('language', 'en-US'),
            'el' => Yii::t('language', 'el'),
            'es' => Yii::t('language', 'es'),
            'et' => Yii::t('language', 'et'),
            'fa-IR' => Yii::t('language', 'fa-IR'),
            'fi' => Yii::t('language', 'fi'),
            'fr' => Yii::t('language', 'fr'),
            'hu' => Yii::t('language', 'hu'),
            'id' => Yii::t('language', 'id'),
            'it' => Yii::t('language', 'it'),
            'ja' => Yii::t('language', 'ja'),
            'kk' => Yii::t('language', 'kk'),
            'ko' => Yii::t('language', 'ko'),
            'lt' => Yii::t('language', 'lt'),
            'lv' => Yii::t('language', 'lv'),
            'nl' => Yii::t('language', 'nl'),
            'pl' => Yii::t('language', 'pl'),
            'pt-BR' => Yii::t('language', 'pt-BR'),
            'pt-PT' => Yii::t('language', 'pt-PT'),
            'ro' => Yii::t('language', 'ro'),
            'ru' => Yii::t('language', 'ru'),
            'sk' => Yii::t('language', 'sk'),
            'sr' => Yii::t('language', 'sr'),
            'sr-Latn' => Yii::t('language', 'sr-Latn'),
            'th' => Yii::t('language', 'th'),
            'uk' => Yii::t('language', 'uk'),
            'vi' => Yii::t('language', 'vi'),
            'zh-CN' => Yii::t('language', 'zh-CN'),
            'zh-TW' => Yii::t('language', 'zh-TW')
        ];
    }

    /**
     * 时区列表
     *
     * @return array
     */
    public static function timezones()
    {
        return [
            'Etc/GMT' => Yii::t('timezone', 'Etc/GMT'),
            'Etc/GMT+0' => Yii::t('timezone', 'Etc/GMT+0'),
            'Etc/GMT+1' => Yii::t('timezone', 'Etc/GMT+1'),
            'Etc/GMT+10' => Yii::t('timezone', 'Etc/GMT+10'),
            'Etc/GMT+11' => Yii::t('timezone', 'Etc/GMT+11'),
            'Etc/GMT+12' => Yii::t('timezone', 'Etc/GMT+12'),
            'Etc/GMT+2' => Yii::t('timezone', 'Etc/GMT+2'),
            'Etc/GMT+3' => Yii::t('timezone', 'Etc/GMT+3'),
            'Etc/GMT+4' => Yii::t('timezone', 'Etc/GMT+4'),
            'Etc/GMT+5' => Yii::t('timezone', 'Etc/GMT+5'),
            'Etc/GMT+6' => Yii::t('timezone', 'Etc/GMT+6'),
            'Etc/GMT+7' => Yii::t('timezone', 'Etc/GMT+7'),
            'Etc/GMT+8' => Yii::t('timezone', 'Etc/GMT+8'),
            'Etc/GMT+9' => Yii::t('timezone', 'Etc/GMT+9'),
            'Etc/UTC' => Yii::t('timezone', 'Etc/UTC'),
            'PRC' => Yii::t('timezone', 'PRC'),
        ];
    }

}
