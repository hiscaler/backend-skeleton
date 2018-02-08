<?php

namespace app\modules\admin\modules\news\models;

use app\models\BaseActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%news}}".
 *
 * @property int $id
 * @property int $category_id 所属分类
 * @property string $title 标题
 * @property string $short_title 副标题
 * @property string $keywords 关键词
 * @property string $description 描述
 * @property string $author 作者
 * @property string $source 来源
 * @property string $source_url 来源 URL
 * @property int $is_picture_news 图片资讯
 * @property string $picture_path 图片地址
 * @property int $enabled 激活
 * @property int $enabled_comment 激活评论
 * @property int $comments_count 评论次数
 * @property int $published_at 发布时间
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
class News extends BaseActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%news}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'is_picture_news', 'comments_count', 'published_at', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title', 'author', 'source', 'published_at'], 'required'],
            [['title', 'short_title', 'keywords', 'author', 'source', 'source_url'], 'trim'],
            [['category_id'], 'default', 'value' => 0],
            [['description'], 'string'],
            [['enabled', 'enabled_comment'], 'boolean'],
            [['title', 'short_title'], 'string', 'max' => 160],
            [['keywords'], 'string', 'max' => 60],
            [['author'], 'string', 'max' => 20],
            [['source'], 'string', 'max' => 30],
            [['source_url', 'picture_path'], 'string', 'max' => 200],
        ];
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
            'short_title' => '副标题',
            'keywords' => '关键词',
            'description' => '描述',
            'author' => '作者',
            'source' => '来源',
            'source_url' => '来源 URL',
            'is_picture_news' => '图片资讯',
            'picture_path' => '图片地址',
            'enabled' => '激活',
            'enabled_comment' => '激活评论',
            'comments_count' => '评论次数',
            'published_at' => '发布时间',
            'created_at' => '添加时间',
            'created_by' => '添加人',
            'updated_at' => '更新时间',
            'updated_by' => '更新人',
        ];
    }

    // Events
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty($this->short_title)) {
                $this->short_title = $this->title;
            }

            return true;
        } else {
            return false;
        }
    }

}
