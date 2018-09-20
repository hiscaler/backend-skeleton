<?php

namespace app\modules\admin\modules\classicCase\models;

use app\models\BaseActiveRecord;
use app\models\FileUploadConfig;
use yadjet\behaviors\ImageUploadBehavior;
use Yii;

/**
 * This is the model class for table "{{%classic_case}}".
 *
 * @property int $id
 * @property int $category_id 所属分类
 * @property string $title 标题
 * @property string $keywords 关键词
 * @property string $description 描述
 * @property string $content 正文
 * @property string $picture_path 案例图片
 * @property int $enabled 激活
 * @property int $clicks_count 点击次数
 * @property int $published_at 发布时间
 * @property int $ordering 排序
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
class ClassicCase extends BaseActiveRecord
{

    /**
     * @var string 文件上传字段
     */
    public $fileFields = 'picture_path';

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
        return '{{%classic_case}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['category_id', 'enabled', 'clicks_count', 'ordering', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title', 'content', 'published_at'], 'required'],
            [['title', 'keywords', 'description'], 'trim'],
            [['description', 'content'], 'string'],
            [['title'], 'string', 'max' => 160],
            [['keywords'], 'string', 'max' => 60],
            ['category_id', 'default', 'value' => 0],
            ['clicks_count', 'default', 'value' => 0],
            ['published_at', 'datetime', 'timestampAttribute' => 'published_at'],
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
            'id' => 'ID',
            'category_id' => '所属分类',
            'title' => '标题',
            'keywords' => '关键词',
            'description' => '描述',
            'content' => '正文',
            'picture_path' => '案例图片',
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
