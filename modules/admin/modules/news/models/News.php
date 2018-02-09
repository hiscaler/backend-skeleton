<?php

namespace app\modules\admin\modules\news\models;

use app\models\BaseActiveRecord;
use app\models\Constant;
use app\models\FileUploadConfig;
use yadjet\behaviors\ImageUploadBehavior;
use Yii;
use yii\web\UploadedFile;

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

    public $_fileUploadConfig;

    public function init()
    {
        $this->_fileUploadConfig = FileUploadConfig::getConfig(static::className2Id(), 'picture_path');
        parent::init();
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
            [['category_id', 'is_picture_news', 'comments_count', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title', 'author', 'source', 'published_at'], 'required'],
            [['title', 'short_title', 'keywords', 'author', 'source', 'source_url'], 'trim'],
            [['category_id'], 'default', 'value' => 0],
            [['description'], 'string'],
            ['published_at', 'datetime', 'format' => 'php:Y-m-d H:i:s', 'timestampAttribute' => 'published_at'],
            [['enabled', 'enabled_comment'], 'boolean'],
            [['title', 'short_title'], 'string', 'max' => 160],
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
                'class' => ImageUploadBehavior::className(),
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

    /**
     * 保存资讯正文内容
     *
     * @param ActiveReocrd $newsContent
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
        return $this->hasOne(NewsContent::className(), ['news_id' => 'id']);
    }

    /**
     * 处理正文内容中的图片，如果没有上传附件图片并且设定了图片的获取位置才会进行解析操作
     *
     * @param ActiveRecord $model
     */
    public function processPicturePath($model)
    {
        if (!(UploadedFile::getInstance($model, 'picture_path') instanceof UploadedFile) && $number = $model->content_image_number) {
            $picturePath = Yad::getTextImages($model->newsContent->content, $number);
            if (!empty($picturePath)) {
                Yii::$app->getDb()->createCommand()->update('{{%news}}', [
                    'is_picture_news' => Option::BOOLEAN_TRUE,
                    'picture_path' => $picturePath,
                ], '[[id]] = :id', [':id' => $model->id])->execute();
            }
        }
    }

    // Events
    private $_newsContent;

    public function afterFind()
    {
        parent::afterFind();
        if (!$this->isNewRecord) {
            $this->published_at = Yii::$app->getFormatter()->asDatetime($this->published_at);
            $this->_newsContent = $this->newsContent ? $this->newsContent->content : null;
        }
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty($this->short_title)) {
                $this->short_title = $this->title;
            }

            $file = UploadedFile::getInstance($this, 'picture_path');
            if ($file instanceof UploadedFile && $file->error != UPLOAD_ERR_NO_FILE) {
                $this->is_picture_news = Constant::BOOLEAN_TRUE;
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        // 清理资讯相关联数据
        $entityName = self::className();
        $db = \Yii::$app->getDb();
        $cmd = $db->createCommand();
        $cmd->delete('{{%news_content}}', ['news_id' => $this->id])->execute();

        // 推送位处理
        $entityAttributes = $db->createCommand('SELECT [[id]], [[label_id]] FROM {{%entity_label}} WHERE [[entity_id]] = :entityId AND [[entity_name]] = :entityName', [':entityId' => $this->id, ':entityName' => $entityName])->queryAll();
        if ($entityAttributes) {
            $entityAttributeIds = $attributeIds = [];
            foreach ($entityAttributes as $entityAttribute) {
                $entityAttributeIds[] = $entityAttribute['id'];
                $attributeIds[] = $entityAttribute['label_id'];
            }
            $cmd->delete('{{%entity_label}}', ['id' => $entityAttributeIds])->execute();
            $db->createCommand('UPDATE {{%label}} SET [[frequency]] = [[frequency]] - 1 WHERE [[id]] IN (' . implode(', ', $attributeIds) . ')')->execute();
        }
    }

}
