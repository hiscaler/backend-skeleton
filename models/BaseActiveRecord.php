<?php

namespace app\models;

use yadjet\helpers\IsHelper;
use yadjet\helpers\StringHelper;
use yadjet\helpers\UtilHelper;
use Yii;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

/**
 * @property string $keywords
 * @property int $created_at
 * @property int $created_by
 * @property int $updated_at
 * @property int $updated_by
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseActiveRecord extends ActiveRecord
{

    /**
     * 默认排序值
     */
    const DEFAULT_ORDERING_VALUE = 10000;

    public function rules()
    {
        $rules = [
            ['content_image_number', 'safe']
        ];

        if ($this->hasAttribute('tags')) {
            $rules[] = [['tags'], 'trim'];
            $rules[] = [['tags'], 'string', 'max' => 255];
            $rules[] = [['tags'], 'normalizeWords'];
        }

        if ($this->hasAttribute('keywords')) {
            $rules[] = [['keywords'], 'trim'];
            $rules[] = [['keywords'], 'string', 'max' => 255];
            $rules[] = [['keywords'], 'normalizeWords'];
        }

        return $rules;
    }

    /**
     * Normalizes the user-entered Words.
     *
     * @param $attribute
     * @param $params
     */
    public function normalizeWords($attribute, $params)
    {
        if (!empty($this->$attribute)) {
            $value = $this->$attribute;
            if (!empty($value)) {
                $value = UtilHelper::array2string(array_unique(UtilHelper::string2array(StringHelper::makeSemiangle($value))));
            }
            $this->$attribute = $value;
        }
    }

    /**
     * 所属分类
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Creater relational
     *
     * @return ActiveQueryInterface the relational query object.
     */
    public function getCreater()
    {
        return $this->hasOne(User::class, ['id' => 'created_by'])->select(['id', 'nickname']);
    }

    /**
     * Updater relational
     *
     * @return ActiveQueryInterface the relational query object.
     */
    public function getUpdater()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by'])->select(['id', 'nickname']);
    }

    /**
     * Deleter relational
     *
     * @return ActiveQueryInterface the relational query object.
     */
    public function getDeleter()
    {
        return $this->hasOne(User::class, ['id' => 'deleted_by'])->select(['id', 'nickname']);
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'short_title' => Yii::t('app', 'Short Title'),
            'tags' => Yii::t('app', 'Tag'),
            'alias' => Yii::t('app', 'Alias'),
            'ordering' => Yii::t('app', 'Ordering'),
            'category_id' => Yii::t('app', 'Category'),
            'group_id' => Yii::t('app', 'Group'),
            'keywords' => Yii::t('app', 'Page Keywords'),
            'description' => Yii::t('app', 'Page Description'),
            'content' => Yii::t('app', 'Content'),
            'screenshot_path' => Yii::t('app', 'Screenshot'),
            'picture_path' => Yii::t('app', 'Picture'),
            'clicks_count' => Yii::t('app', 'Clicks Count'),
            'up_count' => Yii::t('app', 'Up Count'),
            'down_count' => Yii::t('app', 'Down Count'),
            'status' => Yii::t('app', 'Status'),
            'enabled' => Yii::t('app', 'Enabled'),
            'task.status' => Yii::t('app', 'Task Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
            'model_name' => Yii::t('app', 'Model Name'),
            'content_image_number' => Yii::t('app', 'Content Image Number'),
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $userId = IsHelper::cli() || Yii::$app->getUser()->getIsGuest() ? 0 : Yii::$app->getUser()->getId();
            $now = time();
            if ($insert) {
                $this->hasAttribute('created_at') && $this->created_at = $now;
                $this->hasAttribute('created_by') && $this->created_by = $userId;
            }
            $this->hasAttribute('updated_at') && $this->updated_at = $now;
            $this->hasAttribute('updated_by') && $this->updated_by = $userId;

            return true;
        } else {
            return false;
        }
    }

}
