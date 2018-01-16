<?php

namespace app\modules\admin\modules\article\models;

use app\models\BaseActiveRecord;
use Yii;
use yii\helpers\Inflector;

/**
 * This is the model class for table "{{%article}}".
 *
 * @property int $id
 * @property string $alias
 * @property string $title 标题
 * @property string $keyword 关键词
 * @property string $description 描述
 * @property string $content 正文
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
class Article extends BaseActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['title', 'content'], 'required'],
            [['alias', 'title', 'description', 'content'], 'trim'],
            [['description', 'content'], 'string'],
            [['created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['alias', 'title', 'keyword'], 'string', 'max' => 60],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'title' => '标题',
            'keyword' => '关键词',
            'description' => '描述',
            'content' => '正文',
            'creater.nickname' => Yii::t('app', 'Created By'),
            'updater.nickname' => Yii::t('app', 'Updated By'),
        ]);
    }

    // Events
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty($this->alias)) {
                $alias = Inflector::slug($this->title);
                $alias = trim(substr($alias, 0, 60), '-');

                $this->alias = $alias;
            }

            return true;
        } else {
            return false;
        }
    }
}
