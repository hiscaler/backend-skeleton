<?php

namespace app\modules\api\modules\news\models;

use app\models\BaseActiveRecord;
use app\models\FileUploadConfig;
use app\modules\api\extensions\AppHelper;
use yadjet\behaviors\ImageUploadBehavior;

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
 * @property int $clicks_count 点击次数
 * @property int $published_at 发布时间
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
class News extends BaseActiveRecord
{

    /**
     * @var array 文件上传设置
     */
    public $_fileUploadConfig;

    /**
     * @throws \yii\db\Exception
     */
    public function init()
    {
        parent::init();
        $this->_fileUploadConfig = FileUploadConfig::getConfig(static::class, 'picture_path');
    }

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
        return array_merge(parent::rules(), [
            [['category_id', 'is_picture_news', 'comments_count', 'clicks_count', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title', 'author', 'source', 'published_at'], 'required'],
            [['title', 'short_title', 'keywords', 'author', 'source', 'source_url'], 'trim'],
            [['category_id'], 'default', 'value' => 0],
            [['comments_count', 'clicks_count'], 'default', 'value' => 0],
            [['description'], 'string'],
            ['published_at', 'datetime', 'format' => 'php:Y-m-d H:i:s', 'timestampAttribute' => 'published_at'],
            [['enabled', 'enabled_comment'], 'boolean'],
            [['title', 'short_title'], 'string', 'max' => 160],
            [['title'], 'unique'],
            [['keywords'], 'string', 'max' => 60],
            [['author'], 'string', 'max' => 20],
            [['source'], 'string', 'max' => 30],
            [['source_url'], 'string', 'max' => 200],
            ['picture_path', 'image',
                'extensions' => $this->_fileUploadConfig['extensions'],
                'minSize' => $this->_fileUploadConfig['size']['min'],
                'maxSize' => $this->_fileUploadConfig['size']['max'],
            ],
        ]);
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => ImageUploadBehavior::class,
                'attribute' => 'picture_path',
                'thumb' => $this->_fileUploadConfig['thumb']
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
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

    public function fields()
    {
        return [
            'id',
            'category_id',
            'title',
            'short_title',
            'author',
            'source',
            'source_url',
            'keywords',
            'description',
            'is_picture_news',
            'picture_path' => function () {
                return AppHelper::fixStaticAssetUrl($this->picture_path);
            },
            'enabled_comment',
            'comments_count',
            'clicks_count',
            'published_at',
            'created_at',
            'updated_at',
        ];
    }

    public function extraFields()
    {
        return [
            'content' => function () {
                return AppHelper::fixContentAssetUrl($this->newsContent->content);
            }
        ];
    }

    /**
     * 保存资讯正文内容
     *
     * @param NewsContent $newsContent
     * @return boolean
     */
    public function saveContent($newsContent)
    {
        $newsContent->news_id = $this->id;

        return $newsContent->save();
    }

    /**
     * 资讯正文
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewsContent()
    {
        return $this->hasOne(NewsContent::class, ['news_id' => 'id']);
    }

}
