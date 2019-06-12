<?php

namespace app\models;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "{{%set}}".
 *
 * @property string $key 键值
 * @property string $value 值
 */
class Set extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%set}}';
    }

    public function getPrimaryKey($asArray = false)
    {
        return 'key';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['key', 'value'], 'required'],
            ['key', 'trim'],
            [['value'], 'string'],
            [['key'], 'string', 'max' => 100],
            [['key'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'key' => '键值',
            'value' => '值',
        ];
    }

    /**
     * 保存（添加或者更新）
     *
     * @param string $key
     * @param string $value
     * @return bool
     */
    public static function add($key, $value)
    {
        $key = trim($key);
        if (is_string($key) && empty($key) || is_null($key)) {
            return false;
        }

        try {
            return Yii::$app->getDb()->createCommand()->upsert("{{%set}}", ['key' => $key, 'value' => $value])->execute() ? true : false;
        } catch (Exception $e) {
            throw new $e;
        }
    }

    /**
     * 获取指定值
     *
     * @param string $key
     * @param null $defaultValue
     * @return string|null
     * @throws \yii\db\Exception
     */
    public static function get($key, $defaultValue = null)
    {
        return Yii::$app->getDb()->createCommand("SELECT [[value]] FROM {{%set}} WHERE [[key]] = :key", [':key' => trim($key)])->queryScalar() !== false ?: $defaultValue;
    }

    /**
     * 移除项目
     *
     * @param string $key
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function remove($key)
    {
        return Yii::$app->getDb()->createCommand("DELETE FROM {{%set}} WHERE [[key]] = :key", [':key' => trim($key)])->execute() ? true : false;
    }

    /**
     * 清空
     *
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function clean()
    {
        return Yii::$app->getDb()->createCommand()->truncateTable('{{%set}}')->execute() ? true : false;
    }

}
