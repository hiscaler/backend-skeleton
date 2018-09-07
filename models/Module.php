<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "{{%module}}".
 *
 * @property integer $id
 * @property string $alias
 * @property string $name
 * @property string $author
 * @property string $version
 * @property string $icon
 * @property string $url
 * @property string $description
 * @property string $menus
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 */
class Module extends \yii\db\ActiveRecord
{

    const ERROR_NONE = '';
    const ERROR_NOT_FOUND_DIRECTORY = 'Not found directory';
    const ERROR_INVALID_MODULE = 'Invalid module';
    const ERROR_NO_README_FILE = 'No readme file';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%module}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['alias', 'name', 'author', 'version'], 'required'],
            [['alias', 'name', 'author', 'version', 'url', 'description', 'menus'], 'trim'],
            [['created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['alias', 'author'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 30],
            [['version'], 'string', 'max' => 10],
            [['icon', 'url'], 'string', 'max' => 100],
            [['description'], 'string'],
            [['menus'], 'string'],
            [['alias'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'alias' => Yii::t('module', 'Alias'),
            'name' => Yii::t('module', 'Name'),
            'author' => Yii::t('module', 'Author'),
            'version' => Yii::t('module', 'Version'),
            'icon' => Yii::t('module', 'Icon'),
            'url' => Yii::t('module', 'Url'),
            'description' => Yii::t('module', 'Description'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
    }

    /**
     * 获取已经安装的模块
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getInstalledModules()
    {
        return Yii::$app->getDb()->createCommand('SELECT [[id]], [[alias]], [[name]], [[menus]] FROM {{%module}}')->queryAll();
    }

    /**
     * 获取模块别名和名称键值对
     *
     * @return array
     */
    public static function map()
    {
        return (new Query())
            ->select(['name'])
            ->from('{{%module}}')
            ->indexBy('alias')
            ->column();
    }

    // Events
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = $this->updated_at = time();
                $this->created_by = $this->updated_by = Yii::$app->getUser()->getId();
            } else {
                $this->updated_at = time();
                $this->updated_by = Yii::$app->getUser()->getId();
            }

            return true;
        } else {
            return false;
        }
    }
}
