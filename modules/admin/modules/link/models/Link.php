<?php

namespace app\modules\admin\modules\link\models;

use app\models\BaseActiveRecord;
use app\models\Category;
use app\models\FileUploadConfig;
use yadjet\behaviors\ImageUploadBehavior;
use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%link}}".
 *
 * @property int $id
 * @property int $category_id 分类
 * @property int $type 类型
 * @property string $title 标题
 * @property string $description 描叙
 * @property string $url URL
 * @property string $url_open_target 链接打开方式
 * @property string $logo Logo
 * @property int $ordering 排序
 * @property int $enabled 激活
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
class Link extends BaseActiveRecord
{

    /**
     * @var string 文件上传字段
     */
    public $fileFields = 'logo';

    /**
     * 友情链接类型
     */
    const TYPE_TEXT = 0;
    const TYPE_PICTURE = 1;

    /**
     * 链接打开窗口
     */
    const URL_OPEN_TARGET_SELF = '_self';
    const URL_OPEN_TARGET_BLANK = '_blank';

    private $_oldType;
    public $_fileUploadConfig;

    /**
     * @throws \yii\db\Exception
     */
    public function init()
    {
        parent::init();
        $this->_fileUploadConfig = FileUploadConfig::getConfig(static::class, 'logo');
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%link}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'type', 'ordering', 'enabled', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title', 'description', 'url'], 'required'],
            [['title', 'description', 'url'], 'trim'],
            [['title'], 'string', 'max' => 60],
            [['description', 'url'], 'string', 'max' => 100],
            ['url', 'url'],
            [['url_open_target'], 'string', 'max' => 6],
            [['category_id'], 'default', 'value' => 0],
            ['logo', 'image',
                'extensions' => $this->_fileUploadConfig['extensions'],
                'minSize' => $this->_fileUploadConfig['size']['min'],
                'maxSize' => $this->_fileUploadConfig['size']['max'],
                'skipOnEmpty' => false,
                'on' => 'isPictureLink',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => '分类',
            'category.name' => '所属分类',
            'type' => '类型',
            'title' => '标题',
            'description' => '描叙',
            'url' => 'URL',
            'url_open_target' => '链接打开方式',
            'logo' => 'Logo',
            'ordering' => '排序',
            'enabled' => '激活',
            'created_at' => '添加时间',
            'created_by' => '添加人',
            'creater.nickname' => '添加人',
            'updated_at' => '更新时间',
            'updated_by' => '更新人',
            'updater.nickname' => '更新人',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => ImageUploadBehavior::class,
                'attribute' => 'logo',
                'thumb' => $this->_fileUploadConfig['thumb']
            ],
        ];
    }

    public static function typeOptions()
    {
        return [
            self::TYPE_TEXT => Yii::t('link.model', 'Text'),
            self::TYPE_PICTURE => Yii::t('link.model', 'Picture'),
        ];
    }

    public static function urlOpenTargetOptions()
    {
        return [
            self::URL_OPEN_TARGET_SELF => Yii::t('link.model', 'Self'),
            self::URL_OPEN_TARGET_BLANK => Yii::t('link.model', 'Blank')
        ];
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

    // Events
    public function afterFind()
    {
        parent::afterFind();
        $this->_oldType = $this->type;
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if ($this->type == self::TYPE_PICTURE) {
                if ($this->isNewRecord) {
                    $this->setScenario('isPictureLink');
                } else {
                    if ($this->_oldType == self::TYPE_TEXT) {
                        $this->setScenario('isPictureLink');
                    }
                    $file = UploadedFile::getInstance($this, 'logo');
                    if ($file instanceof UploadedFile && $file->error != UPLOAD_ERR_NO_FILE) {
                        $this->setScenario('isPictureLink');
                    }
                }
            }

            return true;
        } else {
            return false;
        }
    }

}
