<?php

namespace app\modules\admin\modules\notice\models;

use app\models\BaseActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%notice}}".
 *
 * @property int $id
 * @property int $category_id 所属分类
 * @property string $title 标题
 * @property string $description 描述
 * @property string $content 正文
 * @property int $enabled 激活
 * @property int $clicks_count 点击次数
 * @property int $published_at 发布时间
 * @property int $ordering 排序
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
class Notice extends BaseActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notice}}';
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DELETE => self::OP_DELETE,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['category_id', 'enabled', 'clicks_count', 'ordering', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title', 'content', 'published_at'], 'required'],
            [['title', 'description'], 'trim'],
            [['description', 'content'], 'string'],
            [['title'], 'string', 'max' => 160],
            ['category_id', 'default', 'value' => 0],
            ['clicks_count', 'default', 'value' => 0],
            ['published_at', 'datetime', 'timestampAttribute' => 'published_at'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => '所属分类',
            'title' => '标题',
            'description' => '描述',
            'content' => '正文',
            'enabled' => '激活',
            'clicks_count' => '点击次数',
            'published_at' => '发布时间',
            'ordering' => '排序',
            'created_at' => '添加时间',
            'created_by' => '添加人',
            'updated_at' => '更新时间',
            'updated_by' => '更新人',
        ];
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function afterFind()
    {
        parent::afterFind();
        if (!$this->isNewRecord) {
            $this->published_at = Yii::$app->getFormatter()->asDatetime($this->published_at);
        }
    }

}
