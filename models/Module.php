<?php

namespace app\models;

use function time;
use Yii;

/**
 * This is the model class for table "www_module".
 *
 * @property integer $id
 * @property string $alias
 * @property string $name
 * @property string $author
 * @property string $version
 * @property string $icon
 * @property string $url
 * @property string $description
 * @property integer $enabled
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 */
class Module extends \yii\db\ActiveRecord
{
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
            [['alias', 'name', 'author', 'version', 'url', 'description'], 'trim'],
            [['enabled', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['alias', 'author'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 30],
            [['version'], 'string', 'max' => 10],
            [['icon', 'url'], 'string', 'max' => 100],
            [['description'], 'string', 'max' => 255],
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
            'enabled' => Yii::t('app', 'Enabled'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
        ];
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
